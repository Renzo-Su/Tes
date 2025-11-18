$(document).ready(function() {
    
    // Event saat tombol Add to Cart diklik
    $('.btn-add-to-cart').on('click', function(e) {
        e.preventDefault();
        
        var btn = $(this);
        var bookId = btn.data('book-id');
        
        // 1. ANIMASI VISUAL: Zoom Gambar ke Tombol
        var card = btn.closest('.card'); // Cari kotak pembungkus terdekat
        var img = card.find('img').eq(0); // Cari gambar di dalam kotak itu

        if (img.length) {
            // Kloning gambar
            var clone = img.clone().offset({
                top: img.offset().top,
                left: img.offset().left
            }).css({
                'opacity': '0.8',
                'position': 'absolute',
                'height': img.height(),
                'width': img.width(),
                'z-index': '1000',
                'border-radius': '10px' // Sedikit melengkung biar bagus
            }).appendTo($('body'));

            // Animasi "Masuk" ke dalam tombol
            clone.animate({
                'top': btn.offset().top,
                'left': btn.offset().left + (btn.width() / 2), // Menuju tengah tombol
                'width': '10px',
                'height': '10px',
                'opacity': '0.1'
            }, 500, function() {
                $(this).remove(); // Hapus kloningan setelah sampai
            });
        }

        // 2. Ubah Tombol Sementara (Loading Effect)
        var originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

        // 3. Kirim AJAX ke Server
        $.ajax({
            type: "POST",
            url: "ajax/add_to_cart.php",
            data: { book_id: bookId },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    // A. Update Angka di Menu Keranjang (Navbar)
                    // Cari link keranjang berdasarkan href
                    var cartLink = $('a[href="cart.php"]'); 
                    // Cari badge angka di dalamnya (jika ada)
                    var badge = cartLink.find('.badge');

                    if (badge.length === 0) {
                        // Jika belum ada badge, buat baru
                        cartLink.append(' <span class="badge bg-danger rounded-pill animate__animated animate__bounceIn">' + response.total_items + '</span>');
                    } else {
                        // Jika sudah ada, update angkanya
                        badge.text(response.total_items);
                        // Efek denyut kecil agar user sadar angka berubah
                        badge.fadeOut(100).fadeIn(100); 
                    }

                    // B. Ubah tombol jadi "Berhasil" (Hijau)
                    btn.removeClass('btn-success').addClass('btn-secondary');
                    btn.html('<i class="fas fa-check"></i> Masuk Keranjang');
                    
                    // Kembalikan tombol seperti semula setelah 1.5 detik
                    setTimeout(function() {
                        btn.removeClass('btn-secondary').addClass('btn-success');
                        btn.html(originalText).prop('disabled', false);
                    }, 1500);

                } else {
                    alert(response.message); // Tampilkan pesan error dari PHP (misal: belum login)
                    btn.html(originalText).prop('disabled', false);
                }
            },
            error: function() {
                alert('Gagal koneksi ke server.');
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});