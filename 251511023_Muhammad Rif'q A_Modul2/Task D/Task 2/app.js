'use strict';

const peserta = [
	{ id: 1, nama: 'Alya', prodi: 'Teknik Informatika' },
	{ id: 2, nama: 'Bima', prodi: 'Sistem Informasi' }
];

const form = document.querySelector('#form-peserta');
const namaInput = document.querySelector('#nama');
const prodiInput = document.querySelector('#prodi');
const filterInput = document.querySelector('#filter-prodi');
const daftar = document.querySelector('#daftar-peserta');
const statusEl = document.querySelector('#status');
const errorNama = document.querySelector('#error-nama');
const errorProdi = document.querySelector('#error-prodi');

function validasiPeserta(nama, prodi) {
	const errNama = nama.trim().length < 3 ? 'Nama minimal 3 karakter.' : '';
	const errProdi = !prodi ? 'Pilih program studi.' : '';
	return { valid: !errNama && !errProdi, errNama, errProdi };
}

function buatKartuPeserta(item) {
	const article = document.createElement('article');
	const h2 = document.createElement('h2');
	const p = document.createElement('p');

	article.classList.add('kartu');
	h2.textContent = item.nama;
	p.textContent = item.prodi;
	article.append(h2, p);
	return article;
}

function renderPeserta(data) {
	daftar.replaceChildren();

	if (data.length === 0) {
		statusEl.textContent = 'Tidak ada peserta.';
		return;
	}

	statusEl.textContent = `Menampilkan ${data.length} peserta.`;
	data.forEach(item => daftar.append(buatKartuPeserta(item)));
}

form.addEventListener('submit', event => {
	event.preventDefault();
	const nama = namaInput.value;
	const prodi = prodiInput.value;
	const { valid, errNama, errProdi } = validasiPeserta(nama, prodi);

	errorNama.textContent = errNama;
	errorProdi.textContent = errProdi;
	namaInput.setAttribute('aria-invalid', String(Boolean(errNama)));
	prodiInput.setAttribute('aria-invalid', String(Boolean(errProdi)));

	if (valid) {
		peserta.push({ id: Date.now(), nama: nama.trim(), prodi });
		form.reset();
		renderPeserta(peserta);
	}
});

filterInput.addEventListener('change', () => {
	const pilihan = filterInput.value;

	if (pilihan === 'semua') {
		renderPeserta(peserta);
	} else {
		const hasilFilter = peserta.filter(p => p.prodi === pilihan);
		renderPeserta(hasilFilter);
	}
});

// Render awal
renderPeserta(peserta);