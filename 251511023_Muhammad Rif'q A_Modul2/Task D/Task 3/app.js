'use strict';

const status = document.querySelector('#status');
const daftar = document.querySelector('#daftar-materi');
const tombolMuat = document.querySelector('#muat');
const tombolCobaLagi = document.querySelector('#coba-lagi');

function aturState(state, pesan) {
  status.dataset.state = state;
  status.textContent = pesan;
  tombolCobaLagi.hidden = state !== 'error';
}

async function ambilMateri() {
  const response = await fetch('materi.json');
  
  if (!response.ok) {
    throw new Error(`Gagal memuat data. Status: ${response.status} - ${response.statusText}`);
  }
  
  return await response.json();
}

function renderMateri(data) {
  daftar.replaceChildren(); // Mengosongkan daftar sebelum merender
  
  data.forEach(item => {
    const kartu = document.createElement('div');
    kartu.className = 'kartu';
    
    const judul = document.createElement('h3');
    judul.style.marginTop = '0';
    judul.textContent = item.judul;
    
    const durasi = document.createElement('p');
    durasi.style.marginBottom = '0';
    durasi.textContent = `Durasi: ${item.durasi} menit`;
    
    kartu.appendChild(judul);
    kartu.appendChild(durasi);
    
    daftar.appendChild(kartu);
  });
}

async function muatData() {
  aturState('loading', 'Memuat data...');
  tombolMuat.disabled = true;
  daftar.replaceChildren();
  
  try {
    const data = await ambilMateri();
    
    if (data.length === 0) {
      aturState('success', 'Data materi kosong.');
    } else {
      renderMateri(data);
      aturState('success', 'Data berhasil dimuat.');
    }
  } catch (error) {
    console.error(error);
    aturState('error', 'Terjadi kesalahan jaringan atau file tidak ditemukan.');
  } finally {
    tombolMuat.disabled = false;
  }
}

tombolMuat.addEventListener('click', muatData);
tombolCobaLagi.addEventListener('click', muatData);