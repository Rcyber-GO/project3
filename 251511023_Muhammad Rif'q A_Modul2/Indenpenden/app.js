function hitungPembayaran(harga, jumlah, isMember) {
    if (harga <= 0 || jumlah <= 0) {
        return { status: "Error", pesan: "Harga dan jumlah harus lebih besar dari nol." };
    }

    const subtotal = harga * jumlah;
    let persentaseDiskon = 0;

    if (subtotal >= 200000) {
        persentaseDiskon = 20;
    } else if (subtotal >= 100000) {
        persentaseDiskon = 10;
    }

    if (isMember) {
        persentaseDiskon += 5;
    }

    if (persentaseDiskon > 25) {
        persentaseDiskon = 25;
    }

    const nominalDiskon = subtotal * (persentaseDiskon / 100);
    const totalBayar = subtotal - nominalDiskon;

    return { status: "Sukses", subtotal, persentaseDiskon, nominalDiskon, totalBayar };
}


/**
 * 2. Bagian Interaksi DOM
 * Menangani event dan menampilkan hasil ke layar[cite: 1].
 */
const formKalkulator = document.querySelector('#form-kalkulator');
const areaHasil = document.querySelector('#area-hasil');
const pesanError = document.querySelector('#pesan-error');
const detailHasil = document.querySelector('#detail-hasil');

// Menangkap event "submit" pada form[cite: 1]
formKalkulator.addEventListener('submit', function(event) {
    // Mencegah halaman reload[cite: 1]
    event.preventDefault(); 
    
    // Mengambil dan mengonversi nilai input menjadi tipe Number[cite: 1]
    const inputHarga = Number(document.querySelector('#harga').value);
    const inputJumlah = Number(document.querySelector('#jumlah').value);
    const inputMember = document.querySelector('#member').checked;

    // Memanggil function logika
    const hasil = hitungPembayaran(inputHarga, inputJumlah, inputMember);

    // Tampilkan area hasil
    areaHasil.classList.remove('hidden');

    if (hasil.status === "Error") {
        // Tampilkan pesan error[cite: 1]
        pesanError.textContent = hasil.pesan;
        pesanError.classList.remove('hidden');
        detailHasil.classList.add('hidden');
    } else {
        // Sembunyikan pesan error dan tampilkan kalkulasi
        pesanError.classList.add('hidden');
        detailHasil.classList.remove('hidden');
        
        // Memperbarui teks pada elemen DOM[cite: 1]
        document.querySelector('#out-subtotal').textContent = `Rp${hasil.subtotal.toLocaleString('id-ID')}`;
        document.querySelector('#out-persen-diskon').textContent = hasil.persentaseDiskon;
        document.querySelector('#out-nominal-diskon').textContent = `Rp${hasil.nominalDiskon.toLocaleString('id-ID')}`;
        document.querySelector('#out-total').textContent = `Rp${hasil.totalBayar.toLocaleString('id-ID')}`;
    }
});