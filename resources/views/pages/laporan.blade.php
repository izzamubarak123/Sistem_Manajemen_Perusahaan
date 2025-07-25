@extends('layouts.app')
@section('title', 'Laporan Paket Bulanan')
@section('content')
    @include('partials.header', ['title' => 'Laporan', 'breadcrumb' => 'Laporan bulan ini'])

    <div class="container mt-4">
        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label for="bulan">Pilih Bulan</label>
                <select name="bulan" id="bulan" class="form-control">
                    @foreach (range(1, 12) as $b)
                        <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($b)->locale('id')->monthName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="tahun">Tahun</label>
                <input type="number" name="tahun" id="tahun" value="{{ $tahun }}" class="form-control">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>
        <p><strong>Periode:</strong> {{ $start }} - {{ $end }}</p>
        <button href="{{ route('laporan.export', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-success">
            <i class="fa fa-file-excel"></i> Export Excel
        </button>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Resi</th>
                        <th>Deskripsi</th>
                        <th>Berat (kg)</th>
                        <th>Volume</th>
                        <th>Jumlah Koli</th>
                        <th>Kota Tujuan</th>
                        <th>Nama Penerima</th>
                        <th>No HP Penerima</th>
                        <th>Vendor</th>
                        <th>Pengirim</th>
                        <th>Biaya vendor</th>
                        <th>Biaya lainnya</th>
                        <th>Pengeluaran</th>
                        <th>Pendapatan</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>

                <tfoot>
                    <tr class="table-secondary">
                        <td colspan="11"></td>
                        <td class="text-right"><strong>Total Bulan ini :</strong></td>
                        <td>
                            Rp
                            {{ number_format(
                                $data->sum(function ($p) {
                                    $biayaVendor = $p->total_vendor ?? $p->vendors->sum('pivot.biaya_vendor');
                                    $biayaLain = is_array($p->biaya_lainnya) ? array_sum($p->biaya_lainnya) : $p->biaya_lainnya ?? 0;
                                    return $biayaVendor + $biayaLain;
                                }),
                                0,
                                ',',
                                '.',
                            ) }}
                        </td>
                        <td>
                            Rp
                            {{ number_format(
                                $data->sum(function ($p) {
                                    $biayaVendor = $p->total_vendor ?? $p->vendors->sum('pivot.biaya_vendor');
                                    $biayaLain = is_array($p->biaya_lainnya) ? array_sum($p->biaya_lainnya) : $p->biaya_lainnya ?? 0;
                                    return ($p->cost ?? 0) - ($biayaVendor + $biayaLain);
                                }),
                                0,
                                ',',
                                '.',
                            ) }}
                        </td>
                    </tr>
                </tfoot>

                <tbody>
                    @forelse ($data as $paket)
                        @php
                            $biayaVendor = $paket->total_vendor ?? $paket->vendors->sum('pivot.biaya_vendor');
                            $biayaLainnya = is_array($paket->biaya_lainnya)
                                ? array_sum($paket->biaya_lainnya)
                                : $paket->biaya_lainnya ?? 0;
                            $pengeluaran = $biayaVendor + $biayaLainnya;
                            $pendapatan = ($paket->cost ?? 0) - $pengeluaran;
                        @endphp
                        <tr>
                            <td>{{ $paket->resi }}</td>
                            <td>{{ $paket->description }}</td>
                            <td>{{ $paket->weight }}</td>
                            <td>{{ $paket->volume }}</td>
                            <td>{{ $paket->jumlah_koli }}</td>
                            <td>{{ $paket->kota_tujuan }}</td>
                            <td>{{ $paket->penerima }}</td>
                            <td>{{ $paket->no_hp_penerima }}</td>
                            <td>{{ $paket->vendors->pluck('name')->implode(', ') ?: '-' }}</td>
                            <td>{{ $paket->creator->name ?? '-' }}</td>
                            <td>Rp {{ number_format($biayaVendor, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($biayaLainnya, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($pengeluaran, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($pendapatan, 0, ',', '.') }}</td>
                            <td>{{ $paket->created_at->format('d-m-Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
