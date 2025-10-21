<div class="kop-surat">
    <div class="kop-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Desa Cisewu" width="80">
    </div>
    <div class="kop-text">
        <h2>PEMERINTAH KABUPATEN GARUT</h2>
        <h2>KECAMATAN CISEWU</h2>
        <h2>DESA CISEWU</h2>
        <p>Alamat: Jalan Wirabhakti Nomor 26 C Cisewu - Garut 44166</p>
        <p>Email: desacisewu1@gmail.com</p>
    </div>
</div>
<hr class="kop-divider">

<style>
    .kop-surat {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 10px;
        width: 100%;
    }
    .kop-logo {
        margin-right: 20px;
        display: flex;
        align-items: center;
    }
    .kop-text {
        text-align: center;
        flex: 1;
    }
    .kop-text h2 {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .kop-text p {
        margin: 2px 0;
        font-size: 12px;
    }
    .kop-divider {
        border: 1px solid #000;
        margin-bottom: 20px;
    }
    @media print {
        .kop-surat {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .kop-logo img {
            width: 80px;
        }
    }
</style>