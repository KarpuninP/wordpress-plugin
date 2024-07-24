
    <!-- Form -->
    <form id="contact-form" action="" method="post">
        <?php wp_nonce_field('ccf_form_nonce_action', 'ccf_form_nonce_field'); ?>
        <input type="hidden" name="action" value="ccf_form_submit">

        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name"  required>

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject"  required>

        <label for="message">Message:</label>
        <textarea id="message" name="message"  required></textarea>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email"  required>

        <input type="submit" name="submit_contact_form" value="Send">
    </form>

    <!-- Modal window -->
    <div id="form-response-modal" style="display:none;">
        <div id="modal-content">
            <span id="close-modal">&times;</span>
            <div id="form-response"></div>
        </div>
    </div>


<script>
    jQuery(document).ready(function($) {
        $('#contact-form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                type: 'POST',
                url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                data: formData,
                success: function(response) {

                    $('#form-response').html(response.data.message);
                    $('#form-response-modal').show();
                },
                error: function(xhr, status, error) {
                    $('#form-response').html('<p>There was an error processing your request. Please try again.</p>');
                    $('#form-response-modal').show();
                }
            });
        });

        $('#close-modal').on('click', function() {
            $('#form-response-modal').hide();
        });
    });
</script>


