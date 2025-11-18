$(document).ready(function() {
    
    // ==========================================================================
    // 1. FITUR VALIDASI INPUT QUANTITY (Pencegahan Input Ngawur)
    // ==========================================================================
    // Event ini berjalan setiap kali user mengetik, mengubah, atau keluar dari kolom input
    $(document).on('keyup change blur', '#quantity', function() {
        var input = $(this);
        var val = parseInt(input.val());
        var max = parseInt(input.attr('max'));
        var min = parseInt(input.attr('min'));

        // A. Jika user mengetik angka lebih besar dari stok
        if (val > max) {
            alert('Maaf, stok yang tersedia hanya ' + max + ' item.');
            input.val(max); // Paksa kembali ke angka max
        }
        
        // B. Jika user mengetik 0 atau minus
        if (val < 1 || isNaN(val)) {
            // Jangan alert terus menerus, cukup paksa balik ke 1
            input.val(1); 
        }
    });

    // ==========================================================================
    // 2. FITUR ADD TO CART (AJAX)
    // ==========================================================================
    $('.btn-add-to-cart').on('click', function(e) {
        e.preventDefault();
        
        var btn = $(this);
        var bookId = btn.data('book-id');
        var quantity = 1; // Default quantity

        // --- A. DETEKSI QUANTITY ---
        // Cek apakah tombol ini ditekan di Halaman Detail (ada input #quantity)
        var detailInput = $('#quantity');
        
        // Logika: Jika ada input #quantity DAN tombol ini berada di dekat input tersebut
        if (detailInput.length > 0 && btn.closest('.col-md-7').length > 0) {
            quantity = parseInt(detailInput.val());
        } 
        // Jika ditekan di Halaman Index (Card), quantity tetap 1.
        
        // Validasi akhir sebelum kirim (Jaga-jaga)
        if (quantity < 1) quantity = 1;


        // --- B. ANIMASI GAMBAR TERBANG KE KERANJANG ---
        var card = btn.closest('.card'); 
        var img = card.find('img').eq(0); 
        
        // Jika di halaman detail, cari gambar utama
        if (img.length === 0) {
            img = $('.col-md-5 img').eq(0);
        }

        if (img.length) {
            var clone = img.clone().offset({
                top: img.offset().top,
                left: img.offset().left
            }).css({
                'opacity': '0.8',
                'position': 'absolute',
                'height': img.height(),
                'width': img.width(),
                'z-index': '1000',
                'border-radius': '10px',
                'object-fit': 'cover'
            }).appendTo($('body'));

            var cartIcon = $('.fa-shopping-cart').closest('a'); // Target ikon keranjang di navbar
            
            if(cartIcon.length) {
                clone.animate({
                    'top': cartIcon.offset().top,
                    'left': cartIcon.offset().left,
                    'width': '20px',
                    'height': '20px',
                    'opacity': '0.1'
                }, 800, function() {
                    $(this).remove(); // Hapus gambar kloningan setelah sampai
                });
            } else {
                clone.fadeOut(500, function(){ $(this).remove(); });
            }
        }


        // --- C. UPDATE TAMPILAN TOMBOL (LOADING) ---
        var originalText = btn.html();
        // Kunci tombol agar tidak bisa diklik berkali-kali saat loading
        btn.html('<i class="fas fa-spinner fa-spin"></i> Proses...').prop('disabled', true);


        // --- D. KIRIM DATA KE SERVER (AJAX) ---
        $.ajax({
            type: "POST",
            url: "ajax/add_to_cart.php", // Pastikan path ini benar sesuai struktur folder Anda
            data: { 
                book_id: bookId,
                quantity: quantity 
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    // 1. Update Angka di Badge Keranjang (Navbar)
                    var cartLink = $('a[href="cart.php"]'); 
                    var badge = cartLink.find('.badge');

                    if (badge.length === 0) {
                        // Jika belum ada badge, buat baru
                        cartLink.append(' <span class="badge bg-danger rounded-pill animate__animated animate__bounceIn">'+ response.total_items +'</span>');
                    } else {
                        // Jika sudah ada, update angkanya
                        badge.text(response.total_items);
                        // Efek visual kedip
                        badge.fadeOut(100).fadeIn(100);
                    }

                    // 2. Feedback Sukses di Tombol
                    btn.removeClass('btn-primary btn-secondary').addClass('btn-success');
                    btn.html('<i class="fas fa-check"></i> Berhasil');
                    
                    // 3. Kembalikan tombol seperti semula setelah 2 detik
                    setTimeout(function() {
                        btn.removeClass('btn-success').addClass('btn-primary'); // Sesuaikan dengan warna asli tombol Anda
                        btn.html(originalText).prop('disabled', false);
                        
                        // Khusus tombol di detail buku, kembalikan class aslinya (hijau)
                        if(btn.hasClass('btn-lg')) {
                             btn.removeClass('btn-primary').addClass('btn-success');
                        }
                    }, 2000);

                } else {
                    // JIKA GAGAL (Misal: Stok Habis di server, atau belum login)
                    alert(response.message);
                    btn.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Terjadi kesalahan sistem atau koneksi.');
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});