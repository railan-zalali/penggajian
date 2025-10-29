@props([
    'leftTitle' => 'Perangkat Desa',
    'leftLabel1' => 'Nama',
    'leftName' => null,
    'rightTitle' => 'Pejabat Berwenang',
    'rightLabel1' => 'Jabatan',
    'rightName' => 'Kepala Desa',
    'paid' => false,
    'paidDate' => null,
])

<style>
    .badge-paid {
        display: inline-block;
        font-size: 10px;
        color: #155724;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 4px;
        padding: 2px 6px;
        margin: 8px 0;
    }
    .signature-area, .signature-box { page-break-inside: avoid; }
</style>

<div class="signature-area">
    <div class="signature-box">
        <div class="signature-line"></div>
        @if($paid)
            <span class="badge-paid">Dibayar</span>
            @if($paidDate)
                <div class="signature-date">Dikonfirmasi {{ $paidDate }}</div>
            @endif
        @endif
        <strong class="signature-name">{{ $leftTitle }}</strong><br>
        @if($leftLabel1)<span class="signature-title">{{ $leftLabel1 }}</span><br>@endif
        @if($leftName)<span class="signature-date">{{ $leftName }}</span>@endif
    </div>
    <div class="signature-box">
        <div class="signature-line"></div>
        <strong class="signature-name">{{ $rightTitle }}</strong><br>
        @if($rightLabel1)<span class="signature-title">{{ $rightLabel1 }}</span><br>@endif
        @if($rightName)<span class="signature-date">{{ $rightName }}</span>@endif
    </div>
</div>