<?php

namespace App\Http\Controllers\Perangkat;

use App\Http\Controllers\Controller;
use App\Models\Penggajian;
use App\Models\DetailPenggajian;
use App\Models\Linmas;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Perangkat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiwayatPenggajianController extends Controller
{
    /**
     * Menampilkan halaman riwayat penggajian untuk perangkat yang sedang login
     */
    public function index(Request $request)
    {
        // Ambil perangkat yang sedang login
        $user = Auth::user();
        $perangkat = Linmas::where('user_id', $user->id)->firstOrFail();

        // Filter berdasarkan tanggal
        $tanggal_awal = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');

        // Filter berdasarkan status pemrosesan dan pembayaran
        $status_pemrosesan = $request->input('status_pemrosesan');
        $status_pembayaran = $request->input('status_pembayaran');

        // Query dasar
        $query = Linmas::where('linmas_id', $perangkat->id)
            ->orderBy('tanggal_penggajian', 'desc');

        // Terapkan filter jika ada
        if ($tanggal_awal && $tanggal_akhir) {
            $query->whereBetween('tanggal_penggajian', [
                Carbon::parse($tanggal_awal)->startOfDay(),
                Carbon::parse($tanggal_akhir)->endOfDay()
            ]);
        }

        if ($status_pemrosesan) {
            $query->where('status_pemrosesan', $status_pemrosesan);
        }

        if ($status_pembayaran) {
            $query->where('status_pembayaran', $status_pembayaran);
        }

        // Ambil data penggajian dengan pagination
        $penggajian = $query->paginate(10);

        return view('perangkat.riwayat-penggajian', compact('penggajian', 'perangkat'));
    }

    /**
     * Menampilkan detail penggajian
     */
    public function detail($id)
    {
        // Ambil perangkat yang sedang login
        $user = Auth::user();
        $perangkat = Perangkat::where('user_id', $user->id)->firstOrFail();

        // Ambil data penggajian
        $penggajian = Payroll::with(['detail', 'verifikator', 'pemberi_persetujuan'])
            ->where('id', $id)
            ->where('perangkat_id', $perangkat->id)
            ->firstOrFail();

        return view('perangkat.detail-penggajian', compact('penggajian', 'perangkat'));
    }

    /**
     * Mengunduh slip gaji dalam format PDF
     */
    public function unduhSlip($id)
    {
        // Ambil perangkat yang sedang login
        $user = Auth::user();
        $perangkat = Perangkat::where('user_id', $user->id)->firstOrFail();

        // Ambil data penggajian
        $penggajian = Payroll::with(['detail', 'verifikator', 'pemberi_persetujuan'])
            ->where('id', $id)
            ->where('perangkat_id', $perangkat->id)
            ->firstOrFail();

        // Ambil detail tunjangan dan potongan
        $tunjangan = PayrollDetail::getTunjangan($penggajian->id);
        $potongan = PayrollDetail::getPotongan($penggajian->id);
        $total_tunjangan = PayrollDetail::getTotalTunjangan($penggajian->id);
        $total_potongan = PayrollDetail::getTotalPotongan($penggajian->id);

        // Generate PDF
        $pdf = Pdf::loadView('perangkat.pdf.slip-gaji', compact(
            'penggajian',
            'perangkat',
            'tunjangan',
            'potongan',
            'total_tunjangan',
            'total_potongan'
        ));

        // Set paper size to A4
        $pdf->setPaper('a4');

        // Download PDF
        $filename = 'slip_gaji_' . $perangkat->nama . '_' .
            Carbon::parse($penggajian->tanggal_penggajian)->format('M_Y') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Menampilkan ringkasan tahunan penggajian
     */
    public function ringkasanTahunan(Request $request)
    {
        // Ambil perangkat yang sedang login
        $user = Auth::user();
        $perangkat = Perangkat::where('user_id', $user->id)->firstOrFail();

        // Ambil tahun dari request, default tahun saat ini
        $tahun = $request->input('tahun', date('Y'));

        // Ambil data penggajian untuk tahun yang dipilih
        $penggajian = Payroll::where('perangkat_id', $perangkat->id)
            ->whereYear('tanggal_penggajian', $tahun)
            ->orderBy('tanggal_penggajian', 'asc')
            ->get();

        // Inisialisasi data bulanan
        $data_bulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $data_bulanan[$i] = [
                'total_hari_hadir' => 0,
                'gaji_pokok' => 0,
                'pembayaran_lembur' => 0,
                'total_tunjangan' => 0,
                'total_potongan' => 0,
                'total_gaji' => 0,
                'status_pembayaran' => null,
                'id' => null,
                'bulan_nama' => Carbon::create($tahun, $i, 1)->format('F'),
                'ada_data' => false
            ];
        }

        // Isi data bulanan dari penggajian
        foreach ($penggajian as $gaji) {
            $bulan = Carbon::parse($gaji->tanggal_penggajian)->month;

            // Hanya hitung gaji yang sudah dibayar atau disetujui
            if ($gaji->status_pembayaran == 'paid' || $gaji->status_pembayaran == 'approved') {
                $data_bulanan[$bulan] = [
                    'total_hari_hadir' => $gaji->total_hari_hadir ?? 0,
                    'gaji_pokok' => $gaji->gaji_pokok ?? 0,
                    'pembayaran_lembur' => $gaji->pembayaran_lembur ?? 0,
                    'total_tunjangan' => PayrollDetail::getTotalTunjangan($gaji->id) ?? 0,
                    'total_potongan' => PayrollDetail::getTotalPotongan($gaji->id) ?? 0,
                    'total_gaji' => $gaji->total_gaji ?? 0,
                    'status_pembayaran' => $gaji->status_pembayaran,
                    'id' => $gaji->id,
                    'bulan_nama' => Carbon::parse($gaji->tanggal_penggajian)->format('F'),
                    'ada_data' => true
                ];
            }
        }

        // Hitung ringkasan tahunan (hanya dari data yang valid)
        $ringkasan = [
            'total_gaji_pokok' => array_sum(array_column($data_bulanan, 'gaji_pokok')),
            'total_lembur' => array_sum(array_column($data_bulanan, 'pembayaran_lembur')),
            'total_tunjangan' => array_sum(array_column($data_bulanan, 'total_tunjangan')),
            'total_potongan' => array_sum(array_column($data_bulanan, 'total_potongan')),
            'total_gaji' => array_sum(array_column($data_bulanan, 'total_gaji')),
            'bulan_terbayar' => count(array_filter($data_bulanan, function ($item) {
                return $item['ada_data'];
            })),
            'rata_rata_gaji' => count(array_filter($data_bulanan, function ($item) {
                return $item['ada_data'];
            })) > 0 ?
                array_sum(array_column($data_bulanan, 'total_gaji')) / count(array_filter($data_bulanan, function ($item) {
                    return $item['ada_data'];
                })) : 0
        ];

        return view('perangkat.ringkasan-tahunan', compact(
            'perangkat',
            'tahun',
            'data_bulanan',
            'ringkasan'
        ));
    }

    /**
     * Mengunduh ringkasan tahunan dalam format PDF
     */
    public function unduhRingkasanTahunan($tahun)
    {
        // Ambil perangkat yang sedang login
        $user = Auth::user();
        $perangkat = Perangkat::where('user_id', $user->id)->firstOrFail();

        // Ambil data penggajian untuk tahun yang dipilih
        $penggajian = Payroll::where('perangkat_id', $perangkat->id)
            ->whereYear('tanggal_penggajian', $tahun)
            ->orderBy('tanggal_penggajian', 'asc')
            ->get();

        // Inisialisasi data bulanan
        $data_bulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $data_bulanan[$i] = [
                'total_hari_hadir' => 0,
                'gaji_pokok' => 0,
                'pembayaran_lembur' => 0,
                'total_tunjangan' => 0,
                'total_potongan' => 0,
                'total_gaji' => 0,
                'bulan_nama' => Carbon::create($tahun, $i, 1)->format('F'),
                'ada_data' => false
            ];
        }

        // Isi data bulanan dari penggajian
        foreach ($penggajian as $gaji) {
            $bulan = Carbon::parse($gaji->tanggal_penggajian)->month;

            // Hanya hitung gaji yang sudah dibayar atau disetujui
            if ($gaji->status_pembayaran == 'paid' || $gaji->status_pembayaran == 'approved') {
                $data_bulanan[$bulan] = [
                    'total_hari_hadir' => $gaji->total_hari_hadir ?? 0,
                    'gaji_pokok' => $gaji->gaji_pokok ?? 0,
                    'pembayaran_lembur' => $gaji->pembayaran_lembur ?? 0,
                    'total_tunjangan' => PayrollDetail::getTotalTunjangan($gaji->id) ?? 0,
                    'total_potongan' => PayrollDetail::getTotalPotongan($gaji->id) ?? 0,
                    'total_gaji' => $gaji->total_gaji ?? 0,
                    'bulan_nama' => Carbon::parse($gaji->tanggal_penggajian)->format('F'),
                    'ada_data' => true
                ];
            }
        }

        // Hitung ringkasan tahunan (hanya dari data yang valid)
        $ringkasan = [
            'total_gaji_pokok' => array_sum(array_column($data_bulanan, 'gaji_pokok')),
            'total_lembur' => array_sum(array_column($data_bulanan, 'pembayaran_lembur')),
            'total_tunjangan' => array_sum(array_column($data_bulanan, 'total_tunjangan')),
            'total_potongan' => array_sum(array_column($data_bulanan, 'total_potongan')),
            'total_gaji' => array_sum(array_column($data_bulanan, 'total_gaji')),
            'bulan_terbayar' => count(array_filter($data_bulanan, function ($item) {
                return $item['ada_data'];
            })),
            'rata_rata_gaji' => count(array_filter($data_bulanan, function ($item) {
                return $item['ada_data'];
            })) > 0 ?
                array_sum(array_column($data_bulanan, 'total_gaji')) / count(array_filter($data_bulanan, function ($item) {
                    return $item['ada_data'];
                })) : 0
        ];

        // Generate PDF
        $pdf = PDF::loadView('perangkat.pdf.ringkasan-tahunan', compact(
            'perangkat',
            'tahun',
            'data_bulanan',
            'ringkasan'
        ));

        // Set paper size to A4 landscape
        $pdf->setPaper('a4', 'landscape');

        // Download PDF
        $filename = 'ringkasan_tahunan_' . $perangkat->nama . '_' . $tahun . '.pdf';

        return $pdf->download($filename);
    }
}
