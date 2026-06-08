<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\MeetingRequests;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataMeetings extends Seeder
{
    public function run(): void
    {
        // ========== USERS ==========
        $users = [
            [
                'name'     => 'Budi Santoso',
                'email'    => 'budi@gmail.com',
                'phone'    => '+6281234567890',
                'address'  => 'Jl. Merdeka No. 1, Purwokerto',
                'role'     => 'user',
                'status'   => 'active',
            ],
            [
                'name'     => 'Siti Rahayu',
                'email'    => 'siti@gmail.com',
                'phone'    => '+6281234567891',
                'address'  => 'Jl. Sudirman No. 5, Banyumas',
                'role'     => 'user',
                'status'   => 'active',
            ],
            [
                'name'     => 'Ahmad Fauzi',
                'email'    => 'ahmad@gmail.com',
                'phone'    => '+6281234567892',
                'address'  => 'Jl. Diponegoro No. 10, Cilacap',
                'role'     => 'user',
                'status'   => 'active',
            ],
            [
                'name'     => 'Dewi Lestari',
                'email'    => 'dewi@gmail.com',
                'phone'    => '+6281234567893',
                'address'  => 'Jl. Ahmad Yani No. 3, Purbalingga',
                'role'     => 'user',
                'status'   => 'active',
            ],
            [
                'name'     => 'Rizky Pratama',
                'email'    => 'rizky@gmail.com',
                'phone'    => '+6281234567894',
                'address'  => 'Jl. Gatot Subroto No. 7, Purwokerto',
                'role'     => 'user',
                'status'   => 'active',
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password'  => Hash::make('password123'),
                    'api_token' => Str::random(60),
                ])
            );
            $createdUsers[] = $user;
        }

        // Ambil admin pertama
        $admin = User::where('role', 'admin')->first();

        // ========== MEETING REQUESTS ==========
        $meetings = [
            // Budi — done (sudah selesai, ada chat)
            [
                'user'        => $createdUsers[0],
                'title'       => 'Pembuatan Website Company Profile',
                'description' => 'Kami membutuhkan website company profile untuk PT Maju Jaya dengan desain modern dan responsif.',
                'date'        => now()->subDays(10)->setTime(9, 0)->format('Y-m-d H:i:s'),
                'time_end'    => '10:00',
                'status'      => 'done',
                'approved_by' => $admin?->id,
                'with_chat'   => true,
            ],
            // Siti — approved (ada chat)
            [
                'user'        => $createdUsers[1],
                'title'       => 'Pengembangan Aplikasi Mobile E-Commerce',
                'description' => 'Membutuhkan aplikasi mobile untuk toko online kami yang terintegrasi dengan sistem pembayaran.',
                'date'        => now()->addDays(3)->setTime(10, 0)->format('Y-m-d H:i:s'),
                'time_end'    => '11:00',
                'status'      => 'approved',
                'approved_by' => $admin?->id,
                'with_chat'   => true,
            ],
            // Ahmad — pending
            [
                'user'        => $createdUsers[2],
                'title'       => 'Konsultasi Sistem Informasi Manajemen',
                'description' => 'Ingin berkonsultasi mengenai implementasi sistem informasi manajemen untuk perusahaan kami.',
                'date'        => now()->addDays(5)->setTime(13, 0)->format('Y-m-d H:i:s'),
                'time_end'    => '14:00',
                'status'      => 'pending',
                'approved_by' => null,
                'with_chat'   => false,
            ],
            // Dewi — pending
            [
                'user'        => $createdUsers[3],
                'title'       => 'Pembuatan Aplikasi Kasir (POS)',
                'description' => 'Memerlukan aplikasi kasir untuk toko retail kami dengan fitur laporan penjualan.',
                'date'        => now()->addDays(7)->setTime(14, 0)->format('Y-m-d H:i:s'),
                'time_end'    => '15:00',
                'status'      => 'pending',
                'approved_by' => null,
                'with_chat'   => false,
            ],
            // Rizky — rejected
            [
                'user'        => $createdUsers[4],
                'title'       => 'Pengembangan Website Sekolah',
                'description' => 'Membutuhkan website sekolah dengan fitur e-learning dan absensi online.',
                'date'        => now()->subDays(2)->setTime(15, 0)->format('Y-m-d H:i:s'),
                'time_end'    => '16:00',
                'status'      => 'rejected',
                'approved_by' => $admin?->id,
                'with_chat'   => false,
            ],
        ];

        foreach ($meetings as $meetingData) {
            $meeting = MeetingRequests::create([
                'user_id'     => $meetingData['user']->id,
                'title'       => $meetingData['title'],
                'description' => $meetingData['description'],
                'date'        => $meetingData['date'],
                'time_end'    => $meetingData['time_end'],
                'status'      => $meetingData['status'],
                'approved_by' => $meetingData['approved_by'],
            ]);

            // ========== CHAT ==========
            if ($meetingData['with_chat'] && $admin) {
                $this->createChat($meeting, $meetingData['user'], $admin);
            }
        }

        $this->command->info('✅ Dummy data berhasil dibuat!');
        $this->command->info('👥 5 user dibuat dengan password: password123');
        $this->command->info('📅 5 meeting request dibuat');
        $this->command->info('💬 2 meeting dengan chat (done & approved)');
    }

    private function createChat(MeetingRequests $meeting, User $user, User $admin): void
    {
        $chats = [
            // Admin mulai
            [
                'sender'  => $admin,
                'message' => 'Halo ' . $user->name . ', terima kasih telah melakukan request meeting. Kami akan segera memproses permintaan Anda.',
                'minutes' => 0,
            ],
            // User balas
            [
                'sender'  => $user,
                'message' => 'Terima kasih admin, kami menunggu konfirmasinya.',
                'minutes' => 5,
            ],
            // Admin
            [
                'sender'  => $admin,
                'message' => 'Meeting telah kami setujui. Mohon hadir tepat waktu sesuai jadwal yang telah ditentukan.',
                'minutes' => 30,
            ],
            // User
            [
                'sender'  => $user,
                'message' => 'Baik admin, kami akan hadir tepat waktu. Apakah ada yang perlu kami persiapkan sebelum meeting?',
                'minutes' => 35,
            ],
            // Admin
            [
                'sender'  => $admin,
                'message' => 'Silakan siapkan dokumen requirement dan gambaran umum project yang ingin dikembangkan.',
                'minutes' => 40,
            ],
            // User
            [
                'sender'  => $user,
                'message' => 'Siap admin, akan kami persiapkan. Terima kasih informasinya.',
                'minutes' => 45,
            ],
        ];

        foreach ($chats as $chat) {
            Message::create([
                'meeting_request_id' => $meeting->id,
                'sender_id'          => $chat['sender']->id,
                'message'            => $chat['message'],
                'created_at'         => now()->subMinutes(count($chats) - $chat['minutes']),
                'updated_at'         => now()->subMinutes(count($chats) - $chat['minutes']),
            ]);
        }
    }
}