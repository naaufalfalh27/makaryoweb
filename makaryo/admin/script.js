// Fungsi untuk menampilkan bagian yang dipilih di dashboard
function showSection(sectionId) {
    const sections = document.querySelectorAll('.section');
    sections.forEach(section => {
        section.style.display = 'none';
    });

    const selectedSection = document.getElementById(sectionId);
    selectedSection.style.display = 'block';
}

// Fungsi untuk menutup popup
function closePopup() {
    document.getElementById('popup').style.display = 'none';
}

// Grafik Penjualan dengan Chart.js
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Sales',
            data: [12000, 15000, 13000, 17000, 20000, 22000],
            borderColor: 'rgba(75, 192, 192, 1)',
            fill: false
        }]
    }
});

// Menangani pengiriman form filter produk
document.getElementById('filterForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const category = document.getElementById('category').value;
    fetchProducts(category);
});

// Fetch produk berdasarkan kategori
function fetchProducts(category) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `fetch_products.php?category=${category}`, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById('productList').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
