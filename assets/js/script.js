$(document).ready(function() {
    
    // ==========================================================================
    // 1. FITUR VALIDASI INPUT QUANTITY (PERBAIKAN UX)
    // ==========================================================================
    // Kita HAPUS 'keyup' agar saat user backspace sampai kosong, tidak otomatis jadi 1
    $(document).on('change blur', '#quantity', function() {
        var input = $(this);
        var val = parseInt(input.val());
        var max = parseInt(input.attr('max'));
        
        // Jika kosong atau bukan angka atau < 1, paksa jadi 1
        if (isNaN(val) || val < 1) {
            input.val(1);
        } 
        // Jika melebihi stok
        else if (val > max) {
            alert('Maksimal pembelian adalah ' + max);
            input.val(max);
        }
    });

    // ==========================================================================
    // 2. FITUR ADD TO CART (AJAX)
    // ==========================================================================
    $('.btn-add-to-cart').on('click', function(e) {
        e.preventDefault();
        
        var btn = $(this);
        var bookId = btn.data('book-id');
        var quantity = 1; 

        // A. DETEKSI QUANTITY
        var detailInput = $('#quantity');
        if (detailInput.length > 0 && btn.closest('.col-md-7').length > 0) { // Pastikan tombol ada di dekat input
            var rawVal = detailInput.val();
            // Cek jika user membiarkan kosong saat klik tombol
            quantity = (rawVal === "") ? 1 : parseInt(rawVal);
        } 
        
        if (quantity < 1) quantity = 1;

        // B. ANIMASI VISUAL
        var card = btn.closest('.card'); 
        // Coba cari gambar di card, kalau tidak ada cari di halaman detail
        var img = card.find('img').eq(0); 
        if (img.length === 0) { img = $('.col-md-5 img').eq(0); }

        if (img.length) {
            var clone = img.clone().offset({
                top: img.offset().top, left: img.offset().left
            }).css({
                'opacity': '0.8', 'position': 'absolute', 'height': img.height(), 'width': img.width(),
                'z-index': '1000', 'border-radius': '10px', 'object-fit': 'cover'
            }).appendTo($('body'));

            var cartIcon = $('.fa-shopping-cart').closest('a');
            // Jika ikon cart ketemu (navbar), terbang kesana. Jika tidak, fadeout aja.
            if(cartIcon.length && cartIcon.is(':visible')) {
                 clone.animate({
                    'top': cartIcon.offset().top, 'left': cartIcon.offset().left,
                    'width': '20px', 'height': '20px', 'opacity': '0.1'
                }, 800, function() { $(this).remove(); });
            } else {
                 clone.animate({ opacity: 0 }, 500, function() { $(this).remove(); });
            }
        }

        // C. TOMBOL LOADING
        var originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Proses...').prop('disabled', true);

        // D. AJAX
        $.ajax({
            type: "POST",
            url: "ajax/add_to_cart.php",
            data: { book_id: bookId, quantity: quantity },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    // Update Badge
                    var cartLink = $('a[href="cart.php"]'); 
                    var badge = cartLink.find('.badge');
                    if (badge.length === 0) {
                        cartLink.append(' <span class="badge bg-danger rounded-pill animate__animated animate__bounceIn">'+ response.total_items +'</span>');
                    } else {
                        badge.text(response.total_items).fadeOut(100).fadeIn(100);
                    }

                    // Feedback Sukses
                    btn.removeClass('btn-primary btn-success').addClass('btn-success');
                    btn.html('<i class="fas fa-check"></i> Berhasil');
                    
                    setTimeout(function() {
                        // Kembalikan warna asli (jika di detail page biasanya success/green, di home primary/blue)
                        if(btn.hasClass('btn-lg')) { 
                            btn.addClass('btn-success'); // Detail page stay green looks better
                        } else {
                            btn.removeClass('btn-success').addClass('btn-primary');
                        }
                        btn.html(originalText).prop('disabled', false);
                    }, 2000);

                } else {
                    alert(response.message);
                    btn.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Gagal koneksi.');
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});