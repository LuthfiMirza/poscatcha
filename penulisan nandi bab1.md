# BAB I
# PENDAHULUAN

## 1.1 Latar Belakang Permasalahan

Perkembangan teknologi informasi saat ini memberikan pengaruh yang besar terhadap berbagai bidang kegiatan, termasuk dalam kegiatan perdagangan dan pengelolaan usaha. Proses bisnis yang sebelumnya banyak dilakukan secara manual mulai beralih ke sistem berbasis komputer agar pekerjaan menjadi lebih cepat, teratur, dan mudah dipantau. Salah satu kegiatan usaha yang membutuhkan dukungan teknologi informasi adalah proses penjualan barang pada toko atau usaha ritel. Dalam kegiatan tersebut, pemilik usaha perlu mengelola data barang, stok, transaksi penjualan, laporan penjualan, hingga aktivitas kasir secara akurat.

Pada usaha yang masih menggunakan pencatatan manual, proses penjualan biasanya dilakukan dengan menulis transaksi di buku, menghitung total pembayaran secara manual, dan memperbarui stok barang setelah transaksi selesai. Cara tersebut masih dapat digunakan untuk jumlah transaksi yang sedikit, tetapi akan menimbulkan kendala apabila jumlah barang dan transaksi semakin banyak. Kendala yang sering terjadi antara lain kesalahan pencatatan harga, kesalahan perhitungan total belanja, stok barang yang tidak sesuai dengan kondisi sebenarnya, kesulitan mencari data transaksi lama, serta lambatnya pembuatan laporan penjualan. Kondisi ini dapat menghambat pemilik usaha dalam mengambil keputusan, misalnya menentukan barang yang perlu ditambah stoknya, melihat produk yang paling banyak terjual, atau mengetahui keuntungan dari penjualan.

Sistem Point of Sale (POS) merupakan salah satu solusi yang dapat digunakan untuk membantu proses penjualan dan pengelolaan data operasional toko. Sistem POS tidak hanya berfungsi sebagai alat untuk mencatat transaksi penjualan, tetapi juga dapat digunakan untuk mengelola data produk, kategori, stok barang, user kasir, riwayat pergerakan stok, laporan penjualan, laporan laba, dan laporan shift kasir. Dengan sistem POS berbasis web, admin dan kasir dapat mengakses sistem melalui browser sesuai dengan hak akses masing-masing. Admin dapat mengelola data master dan memantau laporan, sedangkan kasir dapat melakukan transaksi penjualan kepada pelanggan dengan lebih cepat dan terstruktur.

Proyek yang dikembangkan dalam penulisan ini adalah sistem Point of Sale berbasis web menggunakan framework Laravel. Sistem ini dirancang untuk membantu kegiatan penjualan pada toko melalui dua peran utama, yaitu admin dan kasir. Admin memiliki akses untuk mengelola data produk, kategori, supplier, pembelian stok, user kasir, laporan penjualan, laporan laba, laporan shift, stock movement, serta chatbot admin. Kasir memiliki akses untuk melihat daftar produk, membuka dan menutup shift, melakukan transaksi penjualan, mengatur jumlah barang dalam keranjang, memilih metode pembayaran, menghitung kembalian, menyimpan pending order, melanjutkan transaksi tertunda, dan mencetak struk transaksi.

Pemilihan topik sistem Point of Sale berbasis web dilatarbelakangi oleh kebutuhan toko untuk memiliki sistem yang dapat menyatukan proses penjualan, pencatatan stok, dan penyajian laporan dalam satu aplikasi. Sistem ini diharapkan dapat mengurangi kesalahan pencatatan manual, mempercepat proses transaksi, serta membantu admin memperoleh informasi operasional toko secara lebih mudah. Selain itu, pencatatan pergerakan stok menjadi bagian penting karena setiap perubahan stok, baik karena penambahan produk, pembelian barang, pengubahan data produk, penghapusan produk, maupun transaksi penjualan, perlu memiliki histori yang dapat ditelusuri kembali.

Penelitian atau pengembangan sistem POS serupa sebelumnya umumnya berfokus pada pencatatan transaksi penjualan dan pengelolaan data produk. Namun, sistem yang dikembangkan dalam proyek ini memiliki beberapa pembeda, yaitu adanya pembagian hak akses antara admin dan kasir, fitur pending order, pencatatan stock movement, pengelolaan shift kasir, laporan laba, serta chatbot admin berbasis query database yang bersifat read-only. Chatbot tersebut membantu admin memperoleh ringkasan informasi tertentu, seperti stok produk, produk stok menipis, produk terlaris, ringkasan penjualan, produk mendekati kedaluwarsa, penjualan per kasir, dan riwayat pergerakan stok tanpa harus membuka seluruh halaman laporan secara manual.

Berdasarkan uraian tersebut, maka diperlukan suatu sistem Point of Sale berbasis web yang dapat membantu toko dalam mengelola data produk, stok barang, transaksi penjualan, dan laporan operasional secara lebih efektif. Sistem ini diharapkan dapat menjadi alat bantu bagi admin dan kasir agar proses kerja menjadi lebih cepat, data tersimpan dengan baik, serta informasi penjualan dapat digunakan untuk mendukung pengambilan keputusan.

Berdasarkan latar belakang di atas, maka rumusan masalah dalam penulisan ini adalah sebagai berikut:

1. Bagaimana merancang dan membangun sistem Point of Sale berbasis web yang dapat mengelola data produk, kategori, supplier, pembelian stok, transaksi penjualan, dan laporan toko?
2. Bagaimana menerapkan pembagian hak akses antara admin dan kasir agar setiap pengguna hanya dapat menggunakan fitur sesuai perannya?
3. Bagaimana sistem dapat mencatat perubahan stok barang secara otomatis melalui transaksi penjualan, pembelian stok, dan pengelolaan produk?
4. Bagaimana sistem dapat menyajikan laporan penjualan, laporan laba, laporan shift kasir, dan informasi ringkas melalui chatbot admin?

## 1.2 Ruang Lingkup

Agar pembahasan dalam penulisan ini lebih terarah dan tidak meluas dari permasalahan yang dikaji, maka ruang lingkup penelitian dibatasi pada beberapa hal berikut:

1. Sistem yang dibangun adalah aplikasi Point of Sale berbasis web menggunakan framework Laravel.
2. Sistem memiliki dua jenis pengguna utama, yaitu admin dan kasir.
3. Admin dapat mengelola data produk, kategori, supplier, pembelian stok, user kasir, laporan penjualan, laporan laba, laporan shift kasir, stock movement, profil admin, dan chatbot admin.
4. Kasir dapat melihat daftar produk, membuka shift, melakukan transaksi penjualan, mengelola keranjang transaksi, menyimpan pending order, melakukan checkout, mencetak struk, menutup shift, dan mengelola profil kasir.
5. Data produk yang dikelola meliputi kode produk, nama produk, kategori, gambar produk, harga beli, harga jual, keuntungan, stok, dan tanggal kedaluwarsa.
6. Sistem mencatat pergerakan stok berdasarkan aktivitas tambah produk, update produk atau penyesuaian stok, hapus produk, pembelian stok, dan penjualan produk.
7. Laporan yang dibahas meliputi laporan data penjualan, detail transaksi, laporan laba, laporan shift kasir, dan riwayat stock movement.
8. Chatbot admin yang digunakan bersifat read-only dan hanya mengambil jawaban dari data yang tersedia di database melalui intent parser internal, bukan menggunakan API kecerdasan buatan eksternal.
9. Sistem dikembangkan menggunakan PHP, Laravel, MySQL, Livewire, Blade Template, Bootstrap, NiceAdmin, Vite, dan Spatie Laravel Permission.
10. Penelitian ini tidak membahas integrasi pembayaran dengan payment gateway, integrasi barcode scanner fisik, sistem akuntansi lengkap, dan aplikasi mobile native.

## 1.3 Tujuan Penelitian

Tujuan dari penelitian dan pengembangan sistem ini adalah untuk menghasilkan aplikasi Point of Sale berbasis web yang dapat membantu proses operasional penjualan pada toko. Secara lebih khusus, tujuan penelitian ini adalah sebagai berikut:

1. Merancang dan membangun sistem Point of Sale berbasis web untuk mengelola data produk, kategori, supplier, pembelian stok, transaksi penjualan, dan laporan toko.
2. Menerapkan sistem autentikasi dan hak akses berdasarkan role admin dan kasir agar fitur yang digunakan sesuai dengan tanggung jawab masing-masing pengguna.
3. Membantu kasir dalam melakukan transaksi penjualan, mulai dari pemilihan produk, pengaturan jumlah barang, perhitungan total pembayaran, pemilihan metode pembayaran, perhitungan kembalian, hingga pencetakan struk.
4. Membantu admin dalam mengelola data master, memantau stok barang, melihat riwayat pergerakan stok, dan mengelola user kasir.
5. Menyediakan laporan penjualan, laporan laba, dan laporan shift kasir agar admin dapat memantau kondisi operasional toko.
6. Menyediakan chatbot admin berbasis query database untuk membantu admin memperoleh informasi ringkas mengenai stok, penjualan, produk terlaris, produk mendekati kedaluwarsa, dan pergerakan stok.

## 1.4 Sistematika Penulisan

Sistematika penulisan digunakan untuk memberikan gambaran umum mengenai susunan pembahasan dalam tulisan ilmiah ini. Penulisan ini disusun secara berurutan agar pembaca dapat memahami alur penelitian mulai dari permasalahan, teori pendukung, metode yang digunakan, hasil pengembangan sistem, hingga kesimpulan.

Bab I Pendahuluan berisi uraian mengenai latar belakang permasalahan, rumusan masalah, ruang lingkup, tujuan penelitian, dan sistematika penulisan. Bab ini menjelaskan alasan pemilihan topik sistem Point of Sale berbasis web serta batasan pembahasan yang digunakan dalam penelitian.

Bab II Tinjauan Pustaka berisi teori-teori yang digunakan sebagai dasar dalam pengembangan sistem. Pembahasan pada bab ini dapat mencakup pengertian sistem informasi, Point of Sale, website, database, Laravel, MySQL, UML, autentikasi, hak akses pengguna, transaksi penjualan, stok barang, laporan, serta teori lain yang berhubungan dengan sistem yang dikembangkan.

Bab III Metode Penelitian berisi penjelasan mengenai metode yang digunakan dalam proses penelitian dan pengembangan sistem. Bab ini dapat membahas tahapan pengumpulan data, analisis kebutuhan, perancangan sistem, perancangan database, perancangan antarmuka, implementasi sistem, serta pengujian sistem.

Bab IV Hasil dan Pembahasan berisi hasil implementasi sistem Point of Sale berbasis web dan pembahasan fitur-fitur yang telah dibuat. Pada bab ini dijelaskan tampilan sistem, alur penggunaan aplikasi, fungsi pada halaman admin dan kasir, proses transaksi penjualan, pencatatan stok, laporan, chatbot admin, serta hasil pengujian sistem.

Bab V Penutup berisi kesimpulan dari hasil penelitian dan pengembangan sistem yang telah dilakukan, serta saran untuk pengembangan sistem pada masa mendatang. Kesimpulan disusun berdasarkan tujuan dan hasil pembahasan, sedangkan saran diberikan untuk memperbaiki atau menambahkan fitur yang belum tersedia dalam sistem.
