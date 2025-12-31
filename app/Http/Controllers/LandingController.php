<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sop;
use App\Models\Directorate;
use App\Models\Unit;

class LandingController extends Controller
{
    public function index()
    {
        // Data statistik dari database
        $stats = [
            'sop_count' => Sop::where('status', 'Aktif')->count(),
            'directorate_count' => Directorate::count(),
            'unit_count' => Unit::count(),
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

    /**
     * Get active SOPs for public popup display
     */
    public function getSops(Request $request)
    {
        $search = $request->get('search', '');
        
        $sops = Sop::where('status', 'Aktif')
            ->with('unit:id,id_unit,unit_name')
            ->when($search, function ($query) use ($search) {
                $query->where('sop_name', 'like', "%{$search}%")
                      ->orWhereHas('unit', function ($q) use ($search) {
                          $q->where('unit_name', 'like', "%{$search}%");
                      });
            })
            ->select('id', 'id_sop', 'sop_name', 'id_unit', 'file_path', 'sk_number')
            ->orderBy('sop_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sops->map(function ($sop) {
                return [
                    'id' => $sop->id,
                    'id_sop' => $sop->id_sop,
                    'sop_name' => $sop->sop_name,
                    'sk_number' => $sop->sk_number,
                    'unit_name' => $sop->unit?->unit_name ?? '-',
                    'file_url' => $sop->file_path ? asset('storage/' . $sop->file_path) : null,
                ];
            }),
            'total' => $sops->count(),
        ]);
    }

}
