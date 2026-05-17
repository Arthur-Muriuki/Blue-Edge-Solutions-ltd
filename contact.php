<?php
// 1. Core configurations and database integration
require_once 'includes/db_connect.php';

// Optional: Simple PHP logic to handle form submission feedback (placeholder)
$message_sent = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Here you would typically insert the form data into the database or send an email
    // For now, we just show a success message upon clicking submit
    $message_sent = true;
}

// 2. Inject global site header navigation
include_once 'includes/header.php';
?>

<main style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; padding-bottom: 80px;">

    <section style="background: linear-gradient(135deg, #002d62 0%, #001f42 100%); color: white; padding: 60px 20px; text-align: center;">
        <div style="max-width: 900px; margin: 0 auto;">
            <h1 style="font-size: 2.8rem; font-weight: 700; color:#e2e8f0; margin: 10px 0 15px 0;">Get In Touch</h1>
            <p style="font-size: 1.1rem; color: #cbd5e1; max-width: 600px; margin: 0 auto;">
                Reach out to our engineering team for support, consultations, or to request a custom quote for your enterprise infrastructure.
            </p>
        </div>
    </section>

    <section style="padding: 60px 20px; max-width: 1200px; margin: 0 auto;">
        
        <div style="display: flex; flex-wrap: wrap; gap: 40px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden;">
            
            <div style="flex: 1; min-width: 300px; background: #002d62; color: white; padding: 50px;">
                <h3 style="font-size: 1.8rem; margin-top: 0; margin-bottom: 30px; color: #ff7300;">Contact Information</h3>
                <p style="color: #cbd5e1; line-height: 1.6; margin-bottom: 40px;">
                    Fill up the form and our team will get back to you within 24 hours.
                </p>

                <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 30px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ff7300" viewBox="0 0 16 16" style="flex-shrink: 0; margin-top: 2px;">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                    </svg>
                    <div>
                        <strong style="display: block; font-size: 1.1rem; margin-bottom: 5px;">Head Office</strong>
                        <span style="color: #cbd5e1;">Westlands Commercial Center,<br>Ring Road, Nairobi, Kenya</span>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ff7300" viewBox="0 0 16 16" style="flex-shrink: 0;">
                        <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                    </svg>
                    <div>
                        <strong style="display: block; font-size: 1.1rem; margin-bottom: 5px;">Phone</strong>
                        <span style="color: #cbd5e1;">+254 722 942 293</span>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 15px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ff7300" viewBox="0 0 16 16" style="flex-shrink: 0;">
                        <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414zM0 4.697v7.104l5.803-3.558zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586zm3.436-.586L16 11.801V4.697z"/>
                    </svg>
                    <div>
                        <strong style="display: block; font-size: 1.1rem; margin-bottom: 5px;">Email</strong>
                        <span style="color: #cbd5e1;">info@blueedgesolutions.co.ke</span>
                    </div>
                </div>
            </div>

            <div style="flex: 2; min-width: 300px; padding: 50px;">
                <h3 style="color: #002d62; font-size: 1.8rem; margin-top: 0; margin-bottom: 20px;">Send Us a Message</h3>
                
                <?php if ($message_sent): ?>
                    <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <strong>Success!</strong> Your message has been sent to our support team.
                    </div>
                <?php endif; ?>

                <form action="contact.php" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 200px;">
                            <label for="name" style="display: block; color: #475569; font-weight: bold; margin-bottom: 8px; font-size: 0.9rem;">Full Name *</label>
                            <input type="text" id="name" name="name" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 1rem; font-family: inherit; box-sizing: border-box; outline-color: #ff7300;">
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <label for="email" style="display: block; color: #475569; font-weight: bold; margin-bottom: 8px; font-size: 0.9rem;">Email Address *</label>
                            <input type="email" id="email" name="email" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 1rem; font-family: inherit; box-sizing: border-box; outline-color: #ff7300;">
                        </div>
                    </div>

                    <div>
                        <label for="subject" style="display: block; color: #475569; font-weight: bold; margin-bottom: 8px; font-size: 0.9rem;">Subject *</label>
                        <input type="text" id="subject" name="subject" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 1rem; font-family: inherit; box-sizing: border-box; outline-color: #ff7300;">
                    </div>

                    <div>
                        <label for="message" style="display: block; color: #475569; font-weight: bold; margin-bottom: 8px; font-size: 0.9rem;">Your Message *</label>
                        <textarea id="message" name="message" rows="6" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 1rem; font-family: inherit; resize: vertical; box-sizing: border-box; outline-color: #ff7300;"></textarea>
                    </div>

                    <div style="margin-top: 10px;">
                        <button type="submit" style="background: #ff7300; color: white; border: none; padding: 15px 30px; border-radius: 4px; font-weight: bold; font-size: 1rem; cursor: pointer; transition: background 0.3s;">
                            Submit Message
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </section>

</main>

<?php
// 3. Inject global footer components
include_once 'includes/footer.php';
?>