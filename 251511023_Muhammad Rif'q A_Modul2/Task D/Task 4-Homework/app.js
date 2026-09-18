const stateLoading = document.getElementById('state-loading');
const stateEmpty = document.getElementById('state-empty');
const stateError = document.getElementById('state-error');
const stateContent = document.getElementById('state-content');
const btnRetry = document.getElementById('btn-retry');

let profileData = {};
let skillsArray = [];

// Memuat data dari JSON lokal secara asinkron
async function fetchProfileData() {
    showState('loading');
    
    try {
        // Simulasi delay kecil agar loading state terlihat (opsional, bisa dihapus)
        await new Promise(resolve => setTimeout(resolve, 800));

        const response = await fetch('profile.json');
        
        if (!response.ok) {
            throw new Error('Gagal mengambil data JSON');
        }

        profileData = await response.json();
        
        // Cek jika data kosong
        if (!profileData || Object.keys(profileData).length === 0) {
            showState('empty');
            return;
        }

        skillsArray = profileData.skills || [];
        populateUI();
        showState('content');

    } catch (error) {
        console.error(error);
        showState('error');
    }
}

// Fungsi untuk mengatur tampilan state
function showState(state) {
    stateLoading.classList.add('hidden');
    stateEmpty.classList.add('hidden');
    stateError.classList.add('hidden');
    stateContent.classList.add('hidden');

    if (state === 'loading') stateLoading.classList.remove('hidden');
    else if (state === 'empty') stateEmpty.classList.remove('hidden');
    else if (state === 'error') stateError.classList.remove('hidden');
    else if (state === 'content') stateContent.classList.remove('hidden');
}

// Mengisi elemen HTML dengan data dari JSON
function populateUI() {
    document.getElementById('profile-name').textContent = profileData.name;
    document.getElementById('profile-title').textContent = profileData.title;
    document.getElementById('profile-bio').textContent = profileData.bio;
    renderSkills();
}

// Render list keterampilan
function renderSkills() {
    const skillsList = document.getElementById('skills-list');
    skillsList.innerHTML = ''; // Mengosongkan list terlebih dahulu untuk mencegah penggandaan saat render ulang (Syarat f)

    skillsArray.forEach((skill, index) => {
        const li = document.createElement('li');
        li.textContent = skill;

        // Tombol hapus keterampilan (Syarat e)
        const btnDelete = document.createElement('button');
        btnDelete.textContent = 'Hapus';
        btnDelete.addEventListener('click', () => {
            skillsArray.splice(index, 1);
            renderSkills();
        });

        li.appendChild(btnDelete);
        skillsList.appendChild(li);
    });
}

// Event Listener: Coba Lagi saat Error (Syarat b)
btnRetry.addEventListener('click', fetchProfileData);

// Event Listener: Tombol Detail dengan classList.toggle dan aria-expanded (Syarat c)
const btnDetail = document.getElementById('btn-detail');
const detailContent = document.getElementById('detail-content');

btnDetail.addEventListener('click', () => {
    detailContent.classList.toggle('hidden');
    const isExpanded = !detailContent.classList.contains('hidden');
    btnDetail.setAttribute('aria-expanded', isExpanded.toString());
    btnDetail.textContent = isExpanded ? 'Tutup Detail' : 'Lihat Detail';
});

// Event Listener: Form Tambah Keterampilan dengan Validasi (Syarat d)
const formSkill = document.getElementById('form-skill');
const inputSkill = document.getElementById('input-skill');
const btnAddSkill = document.getElementById('btn-add-skill');

formSkill.addEventListener('submit', (e) => {
    e.preventDefault(); // Mencegah page reload
    
    const newSkill = inputSkill.value.trim();
    
    // Validasi input kosong (Syarat d)
    if (!newSkill) {
        alert('Keterampilan tidak boleh kosong!');
        return;
    }

    // Mencegah klik berulang yang menggandakan data yang sama persis (Syarat f)
    if (skillsArray.includes(newSkill)) {
        alert('Keterampilan ini sudah ada di daftar!');
        return;
    }

    // Menonaktifkan tombol sementara untuk mencegah double-submit cepat (Syarat f tambahan)
    btnAddSkill.disabled = true;
    
    skillsArray.push(newSkill);
    renderSkills();
    
    inputSkill.value = '';
    btnAddSkill.disabled = false;
});

// Event Listener: Ganti Tema
const btnTheme = document.getElementById('btn-theme');
btnTheme.addEventListener('click', () => {
    document.body.classList.toggle('dark-theme');
});

// Inisialisasi awal
fetchProfileData();