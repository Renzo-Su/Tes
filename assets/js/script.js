$(document).ready(function() {
    
    // Event saat tombol Add to Cart diklik
    $('.btn-add-to-cart').on('click', function(e) {
        e.preventDefault();
        
        var btn = $(this);
        var originalText = btn.html(); // Simpan teks asli tombol
        var bookId = btn.data('book-id');
        
        // 1. Ubah Tampilan Tombol (Feedback Visual)
        btn.html('<i class="fas fa-spinner fa-spin"></i> Proses...');
        btn.prop('disabled', true); // Matikan tombol sebentar

        // 2. Kirim AJAX
        $.ajax({
            type: "POST",
            url: "ajax/add_to_cart.php",
            data: { book_id: bookId },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    // A. Ubah Tombol jadi Hijau (Sukses)
                    btn.removeClass('btn-success').addClass('btn-secondary');
                    btn.html('<i class="fas fa-check"></i> Masuk Keranjang');
                    
                    // B. Update Badge Keranjang di Navbar dengan Efek "Denyut"
                    var cartIcon = $('.fa-shopping-cart').eq(0);
                    var navLink = cartIcon.closest('.nav-link');
                    var badge = navLink.find('.badge');
                    
                    // Update angka
                    if (badge.length === 0) {
                        navLink.append(' <span class="badge bg-danger rounded-pill cart-badge">' + response.total_items + '</span>');
                        badge = navLink.find('.badge');
                    } else {
                        badge.text(response.total_items);
                    }

                    // Efek Animasi CSS manual pada Badge
                    badge.css({
                        'transform': 'scale(1.5)',
                        'transition': '0.3s'
                    });
                    setTimeout(function(){ 
                        badge.css('transform', 'scale(1)'); 
                    }, 300);

                    // C. Kembalikan tombol setelah 1 detik
                    setTimeout(function() {
                        btn.html(originalText);
                        btn.prop('disabled', false);
                        btn.removeClass('btn-secondary').addClass('btn-success');
                    }, 1500);

                } else {
                    alert(response.message);
                    btn.html(originalText);
                    btn.prop('disabled', false);
                }
            },
            error: function() {
                alert('Gagal menghubungi server.');
                btn.html(originalText);
                btn.prop('disabled', false);
            }
        });
    });
});