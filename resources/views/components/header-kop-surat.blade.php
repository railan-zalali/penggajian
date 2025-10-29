<div class="kop-surat">
    @php
        $logoPath = public_path('images/logo.png');
        $logoSrc = asset('images/logo.png');
        if (file_exists($logoPath)) {
            $mime = function_exists('mime_content_type') ? mime_content_type($logoPath) : 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    @endphp
    <img src="{{ $logoSrc }}" alt="Logo Garut" class="kop-logo">
    <h2>PEMERINTAH KABUPATEN GARUT</h2>
    <h3>KECAMATAN CISEWU</h3>
    <h3>DESA CISEWU</h3>
    <div class="alamat">
        Alamat: Jalan Wirabuana Nomor 30 C Cisewu - Garut 44166 <br>
        Email: desa.cisewu@gmail.com
    </div>
    <div style="clear: both;"></div>
</div>

<style>
    .kop-surat {
        text-align: center;
        border-bottom: 3px solid black;
        padding-bottom: 10px;
        position: relative;
        margin-bottom: 15px;
        font-family: "Times New Roman", serif;
    }
    .kop-surat img.kop-logo {
        position: absolute;
        left: 50px;
        top: 10px;
        width: 70px;
        height: auto;
    }
    .kop-surat h2, .kop-surat h3, .kop-surat h4 {
        margin: 0;
        line-height: 1.4;
    }
    .kop-surat h2 {
        font-size: 18pt;
    }
    .kop-surat h3 {
        font-size: 14pt;
    }
    .kop-surat h4 {
        font-size: 12pt;
        font-weight: normal;
    }
    .alamat {
        font-size: 10pt;
        margin-top: 5px;
    }
</style>