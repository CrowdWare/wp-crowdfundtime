(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 */

	$(document).ready(function() {
		
		// Donation form submission via AJAX
		$('.wp-crowdfundtime-form').on('submit', function(e) {
			e.preventDefault();
			
			var form = $(this);
			var submitButton = form.find('button[type="submit"]');
			var formData = form.serialize();
			var noticeContainer = form.siblings('.wp-crowdfundtime-notice-container');
			
			// Validate form
			var isValid = true;
			var name = form.find('input[name="name"]').val();
			var email = form.find('input[name="email"]').val();
			var hours = parseInt(form.find('input[name="hours"]').val(), 10);
			
			if (!name) {
				isValid = false;
				form.find('input[name="name"]').addClass('error');
			} else {
				form.find('input[name="name"]').removeClass('error');
			}
			
			if (!email || !isValidEmail(email)) {
				isValid = false;
				form.find('input[name="email"]').addClass('error');
			} else {
				form.find('input[name="email"]').removeClass('error');
			}
			
			if (isNaN(hours) || hours < 1) {
				isValid = false;
				form.find('input[name="hours"]').addClass('error');
			} else {
				form.find('input[name="hours"]').removeClass('error');
			}
			
			if (!isValid) {
				noticeContainer.html(
					'<div class="wp-crowdfundtime-notice error">Please fill in all required fields correctly.</div>'
				);
				return;
			}
			
			// Disable submit button
			submitButton.prop('disabled', true).text('Submitting...');
			
			// AJAX request
			$.ajax({
				url: wp_crowdfundtime_public.ajax_url,
				type: 'POST',
				data: formData + '&action=wp_crowdfundtime_submit_donation&nonce=' + wp_crowdfundtime_public.nonce,
				success: function(response) {
					if (response.success) {
						// Show success message
						noticeContainer.html(
							'<div class="wp-crowdfundtime-notice success">' + response.data.message + '</div>'
						);
						
						// Reset form
						form[0].reset();
						
						// Update progress bars if they exist on the page
						updateProgressBars();
						
						// Update donors list if it exists on the page
						updateDonorsList();
					} else {
						// Show error message
						noticeContainer.html(
							'<div class="wp-crowdfundtime-notice error">' + response.data.message + '</div>'
						);
					}
					
					// Re-enable submit button
					submitButton.prop('disabled', false).text('Zeit spenden');
				},
				error: function() {
					// Show error message
					noticeContainer.html(
						'<div class="wp-crowdfundtime-notice error">An error occurred. Please try again.</div>'
					);
					
					// Re-enable submit button
					submitButton.prop('disabled', false).text('Zeit spenden');
				}
			});
		});
		
		// Social sharing buttons
		$('.wp-crowdfundtime-social-sharing .facebook-button').on('click', function(e) {
			e.preventDefault();
			
			var url = $(this).data('url');
			var title = $(this).data('title');
			
			window.open(
				'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url) + '&t=' + encodeURIComponent(title),
				'facebook-share-dialog',
				'width=626,height=436'
			);
		});
		
		$('.wp-crowdfundtime-social-sharing .x-button').on('click', function(e) {
			e.preventDefault();
			
			var url = $(this).data('url');
			var title = $(this).data('title');
			
			window.open(
				'https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(url),
				'twitter-share-dialog',
				'width=626,height=436'
			);
		});
		
		// Function to validate email
		function isValidEmail(email) {
			var pattern = /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/i;
			return pattern.test(email);
		}
		
		// Function to update progress bars
		function updateProgressBars() {
			$('.wp-crowdfundtime-progress-container').each(function() {
				var container = $(this);
				var campaignId = container.data('campaign-id');
				var type = container.data('type');
				
				if (campaignId) {
					$.ajax({
						url: wp_crowdfundtime_public.ajax_url,
						type: 'POST',
						data: {
							action: 'wp_crowdfundtime_get_progress',
							campaign_id: campaignId,
							type: type,
							nonce: wp_crowdfundtime_public.nonce
						},
						success: function(response) {
							if (response.success) {
								container.replaceWith(response.data.html);
							}
						}
					});
				}
			});
		}
		
		// Function to update donors list
		function updateDonorsList() {
			$('.wp-crowdfundtime-donors-container').each(function() {
				var container = $(this);
				var campaignId = container.data('campaign-id');
				
				if (campaignId) {
					$.ajax({
						url: wp_crowdfundtime_public.ajax_url,
						type: 'POST',
						data: {
							action: 'wp_crowdfundtime_get_donors',
							campaign_id: campaignId,
							nonce: wp_crowdfundtime_public.nonce
						},
						success: function(response) {
							if (response.success) {
								container.replaceWith(response.data.html);
							}
						}
					});
				}
			});
		}
	});

})( jQuery );
