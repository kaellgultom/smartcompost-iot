<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class DashboardController extends Controller
{
    protected string $supabaseUrl;
    protected string $supabaseKey;
    
    protected string $mqttHost;
    protected int $mqttPort;
    protected string $mqttUser;
    protected string $mqttPass;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL');
        $this->supabaseKey = env('SUPABASE_KEY');

        $this->mqttHost = env('MQTT_HOST');
        $this->mqttPort = (int) env('MQTT_PORT_TCP', 8883);
        $this->mqttUser = env('MQTT_USERNAME');
        $this->mqttPass = env('MQTT_PASSWORD');
    }

    public function index()
    {
        $headers = [
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get("{$this->supabaseUrl}/rest/v1/sensor_data?select=*&order=created_at.desc&limit=10");

            if ($response->successful()) {
                $sensorData = $response->json();
            } else {
                Log::error('Supabase Fetch Error: ' . $response->body());
                $sensorData = [];
            }
        } catch (\Exception $e) {
            Log::error('Supabase Connection Error: ' . $e->getMessage());
            $sensorData = [];
        }

        return view('dashboard', compact('sensorData'));
    }

    public function toggleActuator(Request $request)
    {
        $request->validate([
            'device' => 'required|in:motor,kipas,heater', 
            'status' => 'required|boolean',         
        ]);

        $device = $request->input('device');
        $status = $request->input('status');
        $statusString = $status ? 'ON' : 'OFF';
        
        $headers = [
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json',
            'Prefer' => 'return=minimal',
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->patch("{$this->supabaseUrl}/rest/v1/actuators?id=eq.1", [
                    $device => $status
                ]);

            if (!$response->successful()) {
                Log::error('Supabase Toggle Error: ' . $response->body());
                return response()->json(['success' => false, 'message' => 'Gagal mengubah status di database.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Supabase Toggle Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan koneksi ke database.'], 500);
        }

        try {
            $connectionSettings = (new ConnectionSettings)
                ->setUsername($this->mqttUser)
                ->setPassword($this->mqttPass)
                ->setUseTls(true) 
                ->setTlsSelfSignedAllowed(true); 

            $mqtt = new MqttClient($this->mqttHost, $this->mqttPort, 'laravel_backend_' . uniqid());
            $mqtt->connect($connectionSettings, true);

            $payload = json_encode([
                'device' => $device,
                'status' => $statusString
            ]);

            $mqtt->publish('smartcompost/control', $payload, 0);
            $mqtt->disconnect();

            Log::info("MQTT Publish Success: {$device} -> {$statusString}");

        } catch (\Exception $e) {
            Log::error('MQTT Publish Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Database terupdate, tapi gagal kirim perintah ke alat (MQTT Error).'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Perangkat {$device} berhasil di" . ($status ? 'hidupkan' : 'matikan')
        ]);
    }

    /**
     * FITUR AI: Fungsi untuk berkomunikasi dengan Google Gemini AI
     */
    public function aiChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'suhu' => 'nullable|numeric',
            'kelembapan' => 'nullable|numeric',
            'ph' => 'nullable|numeric',
            'status_pintu' => 'nullable|string' 
        ]);

        $userMessage = $request->input('message');
        $suhu = $request->input('suhu', 0);
        $kelembapan = $request->input('kelembapan', 0);
        $statusPintu = $request->input('status_pintu', 'TERTUTUP');

        // SYSTEM PROMPT DIBERSIHKAN DARI SIMBOL * DAN #
        $systemPrompt = "Kamu adalah CompostBot, Asisten Virtual dan AI Core untuk sistem IoT SmartCompost karya Kelompok 16, D-III Teknologi Komputer, Institut Teknologi Del.\n\n"
                      . "BASIS PENGETAHUAN ALAT:\n"
                      . "1. Tujuan Alat: Mempercepat dan mengotomatisasi proses pengomposan sampah organik secara aman menggunakan sistem kelistrikan terpusat (Full DC 24V) berbasis IoT. Alat ini memungkinkan pemantauan suhu dan kelembapan secara real-time, prediksi masa panen, serta perlindungan ganda (safety interlock) bagi penggunanya melalui dashboard cerdas.\n\n"
                      . "2. Tahap-Tahap Operasional SmartCompost:\n"
                      . "Tahap 1: Pencacahan (Shredding). Sampah organik yang dimasukkan akan dicacah oleh Motor DC 30W. Ukuran sampah yang hancur akan mempercepat proses penguraian oleh bakteri.\n"
                      . "Tahap 2: Pemanasan / Inkubasi. Jika suhu awal terlalu dingin, Heater 24V akan aktif untuk menghangatkan ruang reaktor, memicu aktivitas bakteri masuk ke fase termofilik (fase panas).\n"
                      . "Tahap 3: Dekomposisi & Pemantauan. ESP32 terus membaca data suhu dan kelembapan. Kompos secara alami akan memanas dan menyusut.\n"
                      . "Tahap 4: Pematangan (Panen). Fase pengomposan selesai ketika suhu reaktor kembali stabil menurun ke angka suhu ruangan (25-30 derajat Celcius) dan kelembapan terjaga di 40-60 persen.\n\n"
                      . "3. Spesifikasi Teknologi:\n"
                      . "Otak Utama: Mikrokontroler ESP32.\n"
                      . "Sistem Kelistrikan: Aki 12V dengan Modul DC-DC Step-Up (Full DC 24V).\n"
                      . "Aktuator: Motor DC 24V 30W dan Heater 24V (dikontrol via Relay 2-Channel).\n"
                      . "Sensor: Sensor Suhu DS18B20 Waterproof, Sensor Kelembapan, Sensor Pintu Limit Switch ME-8104.\n"
                      . "Catatan Penting: Saat ini Sensor pH belum tersedia pada versi pengembangan ini.\n\n"
                      . "4. Sistem Keamanan (Double Interlock): Motor pencacah dilarang menyala jika Heater sedang aktif, ATAU jika Limit Switch mendeteksi tutup reaktor masih terbuka.\n\n"
                      . "DATA SENSOR REAL-TIME SAAT INI:\n"
                      . "Suhu Reaktor: {$suhu} derajat Celcius\n"
                      . "Kelembapan: {$kelembapan} persen\n"
                      . "Status Tutup: {$statusPintu}\n\n"
                      . "TUGAS ANDA:\n"
                      . "Jawablah pertanyaan pengguna dengan ramah, profesional, dan solutif. Jangan menggunakan format markdown seperti tanda bintang (*) atau pagar (#). Gunakan teks biasa yang rapi. Jika Status Tutup TERBUKA, ingatkan tentang sistem keamanan.\n\n"
                      . "Pertanyaan Pengguna: " . $userMessage;

        try {
            $apiKey = env('GEMINI_API_KEY');
            
            if (empty($apiKey)) {
                return response()->json(['success' => false, 'message' => 'API Key Gemini belum diatur di file .env'], 500);
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $apiKey, 
            ])
            ->withoutVerifying()
            ->timeout(60) 
            ->retry(3, 2000)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemPrompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiReply = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses jawaban saat ini.';
                
                return response()->json([
                    'success' => true,
                    'reply' => $aiReply
                ]);
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? 'Gagal terhubung ke AI.';
            Log::error('Gemini API Error: ' . $response->body());
            
            return response()->json([
                'success' => false, 
                'fallback' => true, 
                'message' => 'Server AI sedang sibuk. Silakan hubungi pembuat alat.',
                'error_detail' => $errorMessage
            ], 500);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'fallback' => true, 
                'message' => 'Koneksi ke AI terputus. Silakan hubungi pembuat alat.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
}