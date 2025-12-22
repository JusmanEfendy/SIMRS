<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Data statistik (bisa dari database)
        $stats = [
            'sop_count' => 156,
            'unit_count' => 24,
            'doc_count' => 1247
        ];

        // Data layanan
        $services = [
            [
                'icon' => 'fa-ambulance',
                'title' => 'IGD 24 Jam',
                'description' => 'Pelayanan gawat darurat dengan sistem triase digital dan monitoring real-time',
                'color' => 'tosca'
            ],
            [
                'icon' => 'fa-bed-pulse',
                'title' => 'Rawat Inap',
                'description' => 'Manajemen kamar dan pasien terintegrasi dengan sistem billing otomatis',
                'color' => 'blue'
            ],
            [
                'icon' => 'fa-microscope',
                'title' => 'Laboratorium',
                'description' => 'Sistem informasi lab dengan interface alat dan hasil digital terintegrasi',
                'color' => 'purple'
            ],
            [
                'icon' => 'fa-user-doctor',
                'title' => 'Poli Spesialis',
                'description' => 'Manajemen jadwal dokter dan antrian online untuk semua poli spesialis',
                'color' => 'green'
            ]
        ];

        return view('landing', compact('stats', 'services'));
    }

}
