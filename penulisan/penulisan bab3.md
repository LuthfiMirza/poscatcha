# BAB III
# METODE PENELITIAN

## 3.1 Objek dan Waktu Penelitian

Penelitian ini mengambil objek pada CATCHA, sebuah usaha yang bergerak di bidang makanan dan minuman. [BUTUH DATA: alamat/lokasi usaha, tahun mulai beroperasi, jumlah kasir/karyawan yang bertugas, dan jam operasional CATCHA — lengkapi dengan data hasil observasi atau keterangan pemilik]. Pemilihan CATCHA sebagai objek didasarkan pada kondisi operasional penjualan yang masih menghadapi kendala pencatatan transaksi, pengelolaan stok bahan baku, dan penanganan pesanan seperti yang sudah diuraikan pada Bab I.

Di CATCHA, transaksi harian ditangani langsung oleh kasir di tempat, sementara permintaan pelanggan yang datang tanpa antre langsung ke lokasi belum tertampung dalam satu alur yang baku. [BUTUH DATA: jumlah rata-rata transaksi per hari, jenis produk yang paling sering dipesan, serta kendala spesifik yang selama ini dirasakan pemilik/kasir CATCHA]. Data-data tersebut nantinya menjadi dasar untuk menilai apakah sistem yang dibangun benar-benar menjawab kebutuhan operasional CATCHA, bukan sekadar mengikuti asumsi umum aplikasi POS.

Waktu penelitian mengikuti jadwal pengembangan sistem yang tercatat pada riwayat kontrol versi (git) proyek ini, yaitu sejak akhir April 2026 sampai dengan penyusunan skripsi ini diselesaikan pada [BUTUH DATA: bulan selesai penyusunan]. Rentang waktu ini mencakup tahap analisis kebutuhan awal, perancangan basis data, penulisan kode program secara bertahap (dimulai dari modul produk dan kategori, dilanjutkan modul transaksi kasir, kemudian modul pembelian dan bahan baku, dan terakhir modul pemesanan online beserta verifikasi QRIS), hingga pengujian sebelum penyusunan laporan.

Penelitian dilakukan secara paralel antara proses pengembangan aplikasi dan proses penggalian kebutuhan dari objek penelitian. Pola kerja semacam ini memang menuntut penyesuaian berulang begitu ada kebutuhan baru yang muncul dari lapangan, tetapi di sisi lain mempermudah validasi setiap fitur langsung terhadap kondisi nyata CATCHA alih-alih menunggu seluruh rancangan selesai lebih dulu.

## 3.2 Alat dan Bahan Penelitian

Perangkat keras yang digunakan selama pengembangan sistem berupa satu unit laptop dengan spesifikasi [BUTUH DATA: prosesor, RAM, dan kapasitas penyimpanan laptop yang dipakai] yang digunakan untuk menulis kode program, menjalankan server lokal, serta melakukan pengujian aplikasi sebelum diakses melalui jaringan. Untuk kebutuhan pengujian pada sisi tampilan, digunakan juga perangkat [BUTUH DATA: perangkat tambahan bila ada, misalnya smartphone untuk menguji tampilan mobile pemesanan online].

Dari sisi perangkat lunak, sistem dibangun menggunakan PHP 8.2 dan framework Laravel 11 sebagai fondasi backend, dipadukan dengan Livewire 3 untuk komponen antarmuka yang membutuhkan interaksi langsung tanpa memuat ulang halaman, misalnya pengelolaan keranjang transaksi kasir. Basis data yang dipakai adalah MySQL, dijalankan melalui XAMPP sebagai lingkungan pengembangan lokal. Manajemen hak akses ditangani oleh paket Spatie Laravel Permission, sementara proses bundling aset frontend (CSS dan JavaScript) memakai Vite. Kode program disusun dengan editor [BUTUH DATA: nama code editor yang dipakai, misalnya Visual Studio Code], dan seluruh perubahan kode dilacak menggunakan Git.

Bahan penelitian mencakup data yang berhubungan langsung dengan objek penelitian, yaitu daftar produk dan kategori yang dijual CATCHA, estimasi harga jual dan harga beli produk, [BUTUH DATA: daftar bahan baku dan resep produk aktual CATCHA bila berbeda dari data uji coba yang dipakai selama pengembangan], serta gambaran alur transaksi yang biasa terjadi di kasir. Selain data primer tersebut, bahan penelitian juga berupa dokumentasi teknis dari framework dan pustaka yang dipakai (Laravel, Livewire, MySQL, Spatie Permission) sebagaimana telah dirujuk pada Bab II.

Kombinasi alat dan bahan di atas dipilih bukan tanpa pertimbangan. Laravel dan Livewire dipilih karena keduanya berada dalam satu ekosistem yang sama sehingga pengembangan fitur interaktif seperti keranjang transaksi tidak perlu menulis JavaScript terpisah, sedangkan MySQL dipilih karena relasi antar data pada sistem ini (produk, resep, transaksi, pesanan) bersifat relasional dan saling terhubung erat.

## 3.3 Metode Pengumpulan Data

Pengumpulan data pada penelitian ini dilakukan melalui beberapa cara yang saling melengkapi. Observasi dilakukan dengan mengamati langsung proses transaksi yang terjadi di CATCHA, mencakup cara kasir mencatat pesanan, cara pembayaran diterima, dan bagaimana stok produk dipantau selama ini. [BUTUH DATA: tanggal dan hasil observasi lapangan di CATCHA, termasuk catatan proses yang diamati].

Wawancara dilakukan kepada pihak yang berhubungan langsung dengan operasional CATCHA, yaitu pemilik usaha dan/atau kasir yang bertugas. Wawancara ini ditujukan untuk menggali permasalahan yang dirasakan dalam pencatatan transaksi manual, kesulitan memantau stok bahan baku, serta harapan terhadap sistem yang akan dibangun. [BUTUH DATA: daftar pertanyaan wawancara beserta ringkasan jawaban narasumber, tanggal wawancara, dan nama/jabatan narasumber sesuai persetujuan yang bersangkutan untuk dicantumkan].

Studi pustaka dilakukan dengan menelusuri jurnal, artikel ilmiah, dan dokumentasi resmi yang berkaitan dengan Point of Sale, pemesanan online, pengelolaan stok berbasis resep, serta pembayaran QRIS, sebagaimana telah diuraikan secara rinci pada Bab II. Studi pustaka ini menjadi rujukan untuk memastikan istilah, konsep, dan pendekatan yang dipakai dalam perancangan sistem konsisten dengan penelitian sejenis yang sudah ada.

Selain ketiga cara di atas, dokumentasi juga dikumpulkan sepanjang proses pengembangan, berupa struktur data yang terbentuk pada basis data (tabel dan relasi), catatan perubahan kode program melalui riwayat commit, serta tangkapan layar antarmuka yang nantinya dipakai pada pembahasan Bab IV. Dokumentasi semacam ini membantu memastikan bahwa uraian pada bab-bab berikutnya benar-benar mencerminkan apa yang sudah diimplementasikan, bukan rencana yang belum terwujud.

## 3.4 Metode Pengembangan Sistem

Sistem pada penelitian ini dikembangkan menggunakan metode Waterfall. Metode ini dipilih karena kebutuhan sistem (transaksi kasir, pengelolaan stok berbasis resep, pemesanan online, verifikasi QRIS manual) sudah dapat dipetakan sejak awal berdasarkan permasalahan yang ditemukan pada Bab I, sehingga tahapan pengembangan dapat disusun secara berurutan tanpa perlu berulang-ulang mengubah arah rancangan besar sistem. Karakteristik Waterfall yang bertahap dan linear juga memudahkan penyusunan laporan skripsi karena setiap tahap menghasilkan dokumen atau luaran yang jelas sebelum berpindah ke tahap berikutnya.

Tahap pertama adalah analisis kebutuhan (requirement analysis), yaitu menggali kebutuhan admin, kasir, dan pembeli melalui observasi dan wawancara sebagaimana diuraikan pada Sub-bab 3.3, kemudian menuangkannya menjadi kebutuhan fungsional dan non-fungsional yang dibahas pada Sub-bab 3.6. Tahap kedua adalah desain sistem (system design), mencakup perancangan basis data, use case, activity diagram, dan struktur antarmuka, sebagaimana dibahas pada Sub-bab 3.7.

Tahap ketiga adalah implementasi (coding), yaitu menerjemahkan hasil rancangan menjadi kode program menggunakan Laravel, Livewire, dan MySQL. Implementasi dilakukan secara modular, dimulai dari modul yang paling mendasar (produk dan kategori) sampai modul yang bergantung pada modul lain (pemesanan online yang membutuhkan data produk dan stok sudah tersedia lebih dulu). Tahap keempat adalah pengujian (testing), memakai metode black box testing terhadap setiap fitur yang sudah dibangun, dengan rincian skenario pengujian pada Sub-bab 3.8. Tahap kelima, pemeliharaan (maintenance), mencakup perbaikan bug yang ditemukan selama pengujian maupun setelah sistem digunakan, termasuk penyesuaian kecil pada alur kerja apabila ditemukan celah pada tahap sebelumnya.

Kelima tahap tersebut digambarkan secara berurutan pada Gambar 3.1. Pada praktiknya tetap ada penyesuaian kecil ketika berpindah dari satu modul ke modul lain, misalnya penambahan tabel riwayat status pesanan setelah modul pemesanan online berjalan. Namun pola besar pengembangan tetap mengikuti urutan Waterfall, bukan iterasi bebas seperti pada Agile atau Scrum.

**[Gambar 3.1 — Tahapan Metode Waterfall pada Pengembangan Sistem]**

## 3.5 Analisis Sistem Berjalan

Sebelum sistem ini dibangun, proses penjualan di CATCHA dijalankan dengan cara [BUTUH DATA: uraikan kondisi nyata proses pencatatan penjualan CATCHA saat ini — apakah masih manual dengan buku/nota, memakai aplikasi kasir sederhana, atau kombinasi keduanya]. Kondisi ini sejalan dengan permasalahan umum yang sudah diuraikan pada latar belakang Bab I, yaitu pencatatan transaksi yang rawan tidak konsisten dan pemantauan stok yang tidak selalu diperbarui tepat waktu.

Pada sisi stok, CATCHA [BUTUH DATA: jelaskan cara pengelolaan stok bahan baku/produk yang berjalan sekarang — dicatat manual, diperkirakan dari pengalaman kasir, atau memakai catatan terpisah] menghadapi kesulitan mengetahui secara pasti kapan bahan baku tertentu perlu dibeli ulang. Tanpa pencatatan yang terhubung ke resep produk, pemilik usaha baru menyadari bahan baku yang hampir habis ketika stok sudah benar-benar menipis, yang berpotensi mengganggu pelayanan pada jam ramai.

Pada sisi pemesanan, pelanggan yang ingin memesan tanpa datang langsung ke lokasi [BUTUH DATA: jelaskan cara pemesanan yang berjalan sekarang, misalnya lewat chat pribadi/WhatsApp, atau memang belum tersedia sama sekali]. Cara ini membutuhkan kasir untuk memeriksa pesan masuk secara manual di sela-sela melayani pelanggan di kasir, sehingga ada risiko pesanan terlewat atau terlambat diproses, terutama saat kasir sedang sibuk melayani transaksi tatap muka.

Berdasarkan gambaran proses berjalan tersebut, tiga persoalan pokok dijadikan dasar perancangan sistem: pencatatan transaksi dan stok yang terpisah-pisah, ketiadaan alur baku untuk pesanan yang datang di luar transaksi tatap muka, dan proses pembayaran nontunai yang belum punya status yang jelas ketika dilakukan tanpa verifikasi otomatis dari penyedia layanan pembayaran. Ketiga persoalan ini menjadi acuan langsung bagi kebutuhan fungsional yang dirumuskan pada Sub-bab 3.6.

## 3.6 Analisis Kebutuhan Sistem

Analisis kebutuhan sistem disusun berdasarkan hasil analisis sistem berjalan pada Sub-bab 3.5 serta ruang lingkup penelitian yang sudah ditetapkan pada Bab I. Kebutuhan dibagi menjadi kebutuhan fungsional, yaitu fitur yang harus disediakan sistem, dan kebutuhan non-fungsional, yaitu kualitas yang harus dipenuhi sistem di luar daftar fitur tersebut.

Pembagian ini penting supaya perancangan pada Sub-bab 3.7 punya acuan yang jelas: setiap use case dan activity diagram yang dirancang harus bisa ditelusuri balik ke salah satu poin kebutuhan fungsional berikut, dan setiap keputusan teknis (pemilihan Laravel, Livewire, struktur middleware) harus bisa dijelaskan kaitannya dengan kebutuhan non-fungsional yang ditetapkan.

### 3.6.1 Kebutuhan Fungsional

Kebutuhan fungsional untuk peran admin meliputi: mengelola data produk (tambah, ubah, hapus, unggah gambar) beserta kategori produk; mengelola data bahan baku dan resep produk agar stok bahan baku otomatis berkurang sesuai komposisi tiap produk; mengelola data supplier dan pembelian bahan baku (purchase order); melihat data penjualan, detail transaksi, laporan laba, dan laporan shift kasir; melihat riwayat pergerakan stok produk maupun bahan baku; mengelola data pengguna kasir; serta menggunakan chatbot admin untuk menanyakan kondisi stok, penjualan, dan operasional toko secara singkat.

Kebutuhan fungsional untuk peran kasir meliputi: membuka dan menutup shift kerja sebelum dan sesudah bertugas; memilih produk ke keranjang transaksi dan mengubah jumlahnya secara langsung; berpindah halaman tanpa kehilangan isi keranjang karena keranjang tersimpan per kasir sampai transaksi diselesaikan; memilih metode pembayaran (Cash, Transfer, atau QRIS) dan menghitung kembalian; mencetak struk setelah transaksi selesai; menerima notifikasi ketika ada pesanan online baru berupa badge, popup, dan bunyi notifikasi; melihat dan mengelola antrian pesanan online dalam tampilan kanban; memverifikasi atau menolak pembayaran QRIS milik pembeli online; serta mengubah status pesanan online dari diterima, diproses, hingga selesai atau dibatalkan.

Kebutuhan fungsional untuk peran pembeli meliputi: melihat daftar menu dan detail produk melalui halaman shop; menambahkan produk ke keranjang beserta kustomisasi bila tersedia (misalnya tingkat es atau gula); melakukan checkout dan memilih metode pembayaran cash atau QRIS manual; melihat detail dan status pesanan yang sudah dibuat; membayar ulang atau menunggu verifikasi apabila pembayaran QRIS sebelumnya ditolak; membatalkan pesanan selama masih berstatus pending; serta mengelola profil akun pembeli.

Selain kebutuhan per peran di atas, sistem juga membutuhkan mekanisme lintas peran berupa pencatatan riwayat setiap perubahan status pesanan dan status pembayaran (siapa yang mengubah, kapan, dari status apa ke status apa, dan catatan tambahan), serta mekanisme pengurangan dan pengembalian stok bahan baku yang konsisten baik untuk transaksi offline di kasir maupun pesanan online, termasuk pengembalian stok otomatis ketika pesanan online yang stoknya sudah dikurangi kemudian dibatalkan.

### 3.6.2 Kebutuhan Non-Fungsional

Aspek keamanan menuntut pemisahan hak akses yang tegas antara admin, kasir, dan pembeli, sehingga satu peran tidak bisa mengakses fitur milik peran lain. Kebutuhan ini dipenuhi dengan middleware autentikasi dan otorisasi berbasis role dari paket Spatie Laravel Permission, dikombinasikan dengan middleware verifikasi email dan pengecekan shift aktif khusus untuk halaman transaksi kasir.

Pada aspek performa, transaksi kasir dan pengurangan stok perlu berjalan cepat dan konsisten meski terjadi hampir bersamaan, misalnya ketika kasir menyelesaikan transaksi offline pada saat yang sama sebuah pesanan online dikonfirmasi dan mengurangi stok bahan baku yang sama. Kebutuhan ini ditangani dengan transaksi basis data (database transaction) dan penguncian baris (lockForUpdate) pada proses yang mengubah angka stok, supaya tidak terjadi kondisi stok minus akibat dua proses yang berjalan bersamaan.

Kegunaan (usability) menjadi perhatian tersendiri pada antarmuka kasir: proses memilih produk, mengubah jumlah, dan menyelesaikan pembayaran dirancang bisa dilakukan dalam jumlah klik yang sesedikit mungkin, mengingat kasir bekerja di bawah tekanan waktu saat melayani pelanggan yang mengantre. Notifikasi pesanan online pada halaman kasir juga dirancang agar tidak mengharuskan kasir berpindah halaman hanya untuk mengetahui ada pesanan baru.

Keandalan data (reliability) dijaga dengan mencatat setiap perubahan stok dan status pesanan sebagai baris riwayat tersendiri (stock movement dan order status history), bukan menimpa nilai lama begitu saja. Pendekatan ini membuat data pada sistem dapat ditelusuri kembali sewaktu-waktu dibutuhkan, misalnya ketika admin perlu mengecek mengapa stok suatu bahan baku berkurang drastis pada tanggal tertentu.

Seluruh kebutuhan fungsional dan non-fungsional di atas selanjutnya diterjemahkan menjadi rancangan sistem yang lebih rinci, mencakup use case diagram, activity diagram, entity relationship diagram, serta struktur tabel basis data. Rancangan tersebut disajikan sebagai hasil perancangan pada Bab IV, sejalan dengan alur pembahasan yang mengikuti tujuan penelitian pada Bab I.

## 3.7 Metode Pengujian Sistem

Pengujian sistem pada penelitian ini menggunakan metode black box testing, yaitu pengujian yang berfokus pada kesesuaian input dan output setiap fitur tanpa memeriksa struktur kode program di baliknya. Metode ini dipilih karena tujuan pengujian adalah memastikan setiap fitur berjalan sesuai kebutuhan fungsional pada Sub-bab 3.6.1, bukan mengevaluasi efisiensi algoritma di dalamnya.

Pengujian dilakukan per modul, dimulai dari modul autentikasi dan hak akses (login admin, login kasir, login pembeli, serta memastikan satu peran tidak bisa mengakses halaman milik peran lain), dilanjutkan modul pengelolaan produk dan kategori (tambah, ubah, hapus, termasuk validasi input yang salah), lalu modul transaksi kasir (pemilihan produk, perubahan jumlah, konsistensi keranjang saat kasir berpindah halaman, perhitungan total dan kembalian, pencetakan struk).

Pengujian berikutnya mencakup modul pemesanan online dan pembayaran, meliputi checkout dengan metode cash maupun QRIS, verifikasi dan penolakan pembayaran QRIS oleh kasir, perubahan status pesanan dari pending sampai completed atau cancelled, serta pengurangan dan pengembalian stok bahan baku pada titik yang tepat (saat konfirmasi dan saat pembatalan). Pengujian turut mencakup modul pembelian bahan baku dari supplier serta modul laporan (penjualan, laba, shift kasir, dan pergerakan stok) untuk memastikan angka yang ditampilkan sesuai dengan data transaksi yang tersimpan.

Setiap skenario pengujian dicatat dalam format tabel yang berisi kolom skenario pengujian, hasil yang diharapkan, hasil yang didapat, dan kesimpulan (valid/tidak valid), sebagaimana akan ditampilkan pada Bab IV. Format pencatatan ini dipilih karena memudahkan penelusuran ulang apabila di kemudian hari ditemukan fitur yang perlu diuji ulang setelah ada perubahan kode program.
