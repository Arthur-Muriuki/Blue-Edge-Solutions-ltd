<?php 
$page_title = "Help Center | Blue Edge Solutions";
include_once 'includes/header.php'; 
?>

<!-- HERO SECTION -->
<section style="background: linear-gradient(135deg, #002d62 0%, #00122e 100%); color: white; padding: 120px 20px; text-align: center; min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center; border-bottom: 5px solid #ff7300;">
    <div style="max-width: 800px; margin: 0 auto;">
        
        <h1 style="font-size: 3.5rem; margin-bottom: 20px; color:#cbd5e1; font-weight: 800; line-height: 1.2; letter-spacing: -1px;">
            Help Center & FAQs
        </h1>
        
        <p style="font-size: 1.25rem; color: #cbd5e1; margin-bottom: 40px; line-height: 1.6;">
            Find quick answers to common questions about our hardware shop, cloud services, and IT support.
        </p>
        
        <a href="contact.php" style="background-color: #ff7300; color: white; padding: 15px 40px; text-decoration: none; font-size: 1.2rem; font-weight: bold; border-radius: 4px; box-shadow: 0 4px 15px rgba(255, 115, 0, 0.3); display: inline-block;">
            Contact Support
        </a>

    </div>
</section>

<!-- MAIN FAQ CONTENT -->
<main style="background-color: #f8fafc; padding: 80px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        
        <div style="text-align: center; margin-bottom: 50px;">
            <h2 style="color: #002d62; font-size: 2.5rem; margin-bottom: 15px; font-weight: bold;">Frequently Asked Questions</h2>
            <p style="color: #475569; font-size: 1.1rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                Everything you need to know about purchasing hardware, managing IT services, and technical support.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            
            <!-- Category 1: Hardware & Orders -->
            <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: left; border-top: 4px solid #ff7300;">
                <div style="font-size: 2.5rem; margin-bottom: 15px;">📦</div>
                <h3 style="color: #002d62; font-size: 1.4rem; margin-bottom: 15px;">Hardware & Shipping</h3>
                
                <h4 style="color: #002d62; font-size: 1rem; margin: 15px 0 5px 0;">What warranty comes with equipment?</h4>
                <p style="color: #64748b; line-height: 1.6; margin-bottom: 15px;">
                    All enterprise networking equipment comes with a standard 1-year manufacturer warranty.
                </p>

                <h4 style="color: #002d62; font-size: 1rem; margin: 15px 0 5px 0;">How long does shipping take?</h4>
                <p style="color: #64748b; line-height: 1.6; margin-bottom: 25px;">
                    Standard local delivery takes 1–3 business days across Kenya.
                </p>
                <a href="shop.php" style="color: #ff7300; font-weight: bold; text-decoration: none;">Visit Shop &rarr;</a>
            </div>

            <!-- Category 2: Cyber & Infrastructure -->
            <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: left; border-top: 4px solid #002d62;">
                <div style="font-size: 2.5rem; margin-bottom: 15px;">🛡️</div>
                <h3 style="color: #002d62; font-size: 1.4rem; margin-bottom: 15px;">Services & Security</h3>
                
                <h4 style="color: #002d62; font-size: 1rem; margin: 15px 0 5px 0;">Are your solutions compliant?</h4>
                <p style="color: #64748b; line-height: 1.6; margin-bottom: 15px;">
                    Yes, all cloud and security setups adhere strictly to the Kenyan Data Protection Act.
                </p>

                <h4 style="color: #002d62; font-size: 1rem; margin: 15px 0 5px 0;">Do you perform on-site audits?</h4>
                <p style="color: #64748b; line-height: 1.6; margin-bottom: 25px;">
                    We conduct complete infrastructure and security vulnerability assessments.
                </p>
                <a href="services.php" style="color: #002d62; font-weight: bold; text-decoration: none;">Our Services &rarr;</a>
            </div>

            <!-- Category 3: Consultations & SLAs -->
            <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: left; border-top: 4px solid #ff7300;">
                <div style="font-size: 2.5rem; margin-bottom: 15px;">⏱️</div>
                <h3 style="color: #002d62; font-size: 1.4rem; margin-bottom: 15px;">Support & Hours</h3>
                
                <h4 style="color: #002d62; font-size: 1rem; margin: 15px 0 5px 0;">What are your support hours?</h4>
                <p style="color: #64748b; line-height: 1.6; margin-bottom: 15px;">
                    Standard helpdesk is available Mon–Fri (8 AM – 5 PM). Premium SLA clients enjoy 24/7 support.
                </p>

                <h4 style="color: #002d62; font-size: 1rem; margin: 15px 0 5px 0;">How do I request a consultation?</h4>
                <p style="color: #64748b; line-height: 1.6; margin-bottom: 25px;">
                    Click below to fill out our consultation request form and an engineer will contact you.
                </p>
                <a href="contact.php" style="color: #ff7300; font-weight: bold; text-decoration: none;">Request Consultation &rarr;</a>
            </div>

        </div>

    </div>
</main>

<!-- GET IN TOUCH / CALLOUT SECTION -->
<section style="background-color: #ffffff; padding: 80px 20px; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 50px; align-items: center;">
        
        <div style="flex: 1; min-width: 300px;">
            <h2 style="color: #002d62; font-size: 2.5rem; margin-bottom: 20px; font-weight: bold;">Still Need Assistance?</h2>
            <div style="width: 60px; height: 4px; background-color: #ff7300; margin-bottom: 25px;"></div>
            <p style="color: #475569; font-size: 1.1rem; line-height: 1.7; margin-bottom: 20px;">
                Can't find the answer you are looking for? Reach out to our technical team for custom network configurations, hardware inquiries, or immediate IT assistance.
            </p>
            <p style="color: #475569; font-size: 1.1rem; line-height: 1.7;">
                We are dedicated to providing enterprise-grade support to keep your operations running smoothly.
            </p>
        </div>

        <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; gap: 30px;">
            
            <div style="display: flex; gap: 20px; align-items: flex-start;">
                <div style="font-size: 2rem; background: #fff7ed; padding: 15px; border-radius: 8px; color: #ff7300; box-shadow: 0 2px 10px rgba(255,115,0,0.1);">✉️</div>
                <div>
                    <h4 style="color: #002d62; font-size: 1.2rem; margin: 0 0 8px 0; font-weight: bold;">Email Support</h4>
                    <p style="color: #64748b; margin: 0; line-height: 1.5;">Send us your query at <strong>info@blueedge-sl.com</strong></p>
                </div>
            </div>

            <div style="display: flex; gap: 20px; align-items: flex-start;">
                <div style="font-size: 2rem; background: #fff7ed; padding: 15px; border-radius: 8px; color: #ff7300; box-shadow: 0 2px 10px rgba(255,115,0,0.1);">📞</div>
                <div>
                    <h4 style="color: #002d62; font-size: 1.2rem; margin: 0 0 8px 0; font-weight: bold;">Phone Assistance</h4>
                    <p style="color: #64748b; margin: 0; line-height: 1.5;">Speak to our help desk: <strong>+254 722 942 293 / +254 733 775 544</strong></p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- FLOATING AI CHATBOT WIDGET -->
<div id="ai-chat-launcher" onclick="toggleChatbot()" style="position: fixed; bottom: 105px; right: 30px; background-color: #ff7300; color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(255,115,0,0.4); cursor: pointer; z-index: 1000; font-size: 1.5rem;">
    💬
</div>

<div id="ai-chat-box" style="display: none; position: fixed; bottom: 175px; right: 30px; width: 350px; max-width: 90vw; height: 480px; background: white; border-radius: 12px; box-shadow: 0 5px 25px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; flex-direction: column; overflow: hidden; z-index: 1000;">
    
    <!-- Chat Header -->
    <div style="background: linear-gradient(135deg, #002d62 0%, #00122e 100%); color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #ff7300;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="font-size: 1.2rem;">🤖</div>
            <div>
                <strong style="display: block; font-size: 0.95rem;">Blue Edge AI Assistant</strong>
                <span style="font-size: 0.75rem; color: #cbd5e1;">Online • Enterprise Support</span>
            </div>
        </div>
        <button onclick="toggleChatbot()" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer;">✕</button>
    </div>

    <!-- Chat Messages Window -->
    <div id="chat-messages" style="flex: 1; padding: 15px; overflow-y: auto; background: #f8fafc; font-size: 0.9rem; line-height: 1.5;">
        <div style="background: #e2e8f0; color: #002d62; padding: 10px 14px; border-radius: 8px; margin-bottom: 10px; max-width: 85%;">
            Hello! I'm your Blue Edge AI assistant. How can I help you today?
        </div>
    </div>

    <!-- Chat Input Form -->
    <form id="chat-form" onsubmit="sendChatMessage(event)" style="display: flex; border-top: 1px solid #e2e8f0; padding: 10px; background: white;">
        <input type="text" id="chat-input" placeholder="Ask a question..." required style="flex: 1; border: 1px solid #cbd5e1; padding: 10px; border-radius: 6px; outline: none; font-size: 0.9rem;">
        <button type="submit" style="background: #ff7300; color: white; border: none; padding: 10px 15px; margin-left: 8px; border-radius: 6px; cursor: pointer; font-weight: bold;">Send</button>
    </form>
</div>

<!-- CHATBOT JAVASCRIPT LOGIC -->
<script>
function toggleChatbot() {
    const chatBox = document.getElementById('ai-chat-box');
    chatBox.style.display = (chatBox.style.display === 'none' || chatBox.style.display === '') ? 'flex' : 'none';
}

async function sendChatMessage(e) {
    e.preventDefault();
    const input = document.getElementById('chat-input');
    const messages = document.getElementById('chat-messages');
    const userMsg = input.value.trim();

    if (!userMsg) return;

    // Append User Message
    const userBubble = document.createElement('div');
    userBubble.style.cssText = 'background: #002d62; color: white; padding: 10px 14px; border-radius: 8px; margin-bottom: 10px; max-width: 85%; margin-left: auto; text-align: right;';
    userBubble.textContent = userMsg;
    messages.appendChild(userBubble);

    input.value = '';
    messages.scrollTop = messages.scrollHeight;

    // Append Thinking Indicator
    const botBubble = document.createElement('div');
    botBubble.style.cssText = 'background: #e2e8f0; color: #002d62; padding: 10px 14px; border-radius: 8px; margin-bottom: 10px; max-width: 85%;';
    botBubble.textContent = 'Thinking...';
    messages.appendChild(botBubble);
    messages.scrollTop = messages.scrollHeight;

    try {
        const response = await fetch('api/chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: userMsg })
        });
        const data = await response.json();
        botBubble.textContent = data.reply || 'Sorry, something went wrong.';
    } catch (err) {
        botBubble.textContent = 'Unable to reach assistant. Please check your connection.';
    }

    messages.scrollTop = messages.scrollHeight;
}
</script>

<?php include_once 'includes/footer.php'; ?>