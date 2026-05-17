<?php
// 1. Core configurations and database integration
require_once 'includes/db_connect.php';

// 2. Inject global site header navigation
include_once 'includes/header.php';
?>

<main style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; padding-bottom: 80px;">

    <section style="background: linear-gradient(135deg, #002d62 0%, #001f42 100%); color: white; padding: 60px 20px; text-align: center;">
        <div style="max-width: 900px; margin: 0 auto;">
            <h1 style="font-size: 2.8rem; font-weight: 700; color:#e2e8f0; margin: 10px 0 15px 0;">Hardware & Service Shop</h1>
            <p style="font-size: 1.1rem; color: #cbd5e1; max-width: 600px; margin: 0 auto;">
                Upgrade your infrastructure with enterprise-grade networking equipment or book our specialized IT service packages directly.
            </p>
        </div>
    </section>

    <section style="padding: 60px 20px; max-width: 1200px; margin: 0 auto;">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                <div style="background: #e2e8f0; height: 200px; display: flex; align-items: center; justify-content: center; color: #64748b;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m2.07 11.207a.5.5 0 0 1-.707.008C8.5 10.454 7.5 10.454 6.637 11.215a.5.5 0 1 1-.674-.738c1.176-1.074 2.9-1.074 4.074 0a.5.5 0 0 1 .033.73zM5.145 9.878a.5.5 0 1 1-.724-.69C5.83 7.75 10.17 7.75 11.58 9.188a.5.5 0 0 1-.724.69c-1.09-1.053-4.32-1.053-5.71 0zM3.66 8.358a.5.5 0 0 1-.742-.67c1.92-2.124 7.243-2.124 9.164 0a.5.5 0 1 1-.742.67c-1.503-1.663-5.683-1.663-7.68 0z"/>
                    </svg>
                </div>
                <div style="padding: 25px; display: flex; flex-direction: column; flex-grow: 1;">
                    <span style="color: #64748b; font-size: 0.85rem; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Networking</span>
                    <h3 style="color: #002d62; font-size: 1.25rem; margin: 0 0 10px 0;">Enterprise Wi-Fi 6 Router</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px; flex-grow: 1;">High-capacity tri-band router designed for heavy office traffic and secure VPN tunneling.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.4rem; font-weight: bold; color: #002d62;">Ksh 35,000</span>
                        <button style="background: #ff7300; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer;">Add to Cart</button>
                    </div>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                <div style="background: #e2e8f0; height: 200px; display: flex; align-items: center; justify-content: center; color: #64748b;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m.5 4.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M5 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M5.5 10a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3m6 1a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m.5 4.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
                    </svg>
                </div>
                <div style="padding: 25px; display: flex; flex-direction: column; flex-grow: 1;">
                    <span style="color: #64748b; font-size: 0.85rem; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Networking</span>
                    <h3 style="color: #002d62; font-size: 1.25rem; margin: 0 0 10px 0;">24-Port Managed Switch</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px; flex-grow: 1;">Gigabit PoE+ switch for seamless scaling of IP cameras, VoIP phones, and access points.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.4rem; font-weight: bold; color: #002d62;">Ksh 55,000</span>
                        <button style="background: #ff7300; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer;">Add to Cart</button>
                    </div>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                <div style="background: #e2e8f0; height: 200px; display: flex; align-items: center; justify-content: center; color: #64748b;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                        <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                    </svg>
                </div>
                <div style="padding: 25px; display: flex; flex-direction: column; flex-grow: 1;">
                    <span style="color: #64748b; font-size: 0.85rem; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Hardware</span>
                    <h3 style="color: #002d62; font-size: 1.25rem; margin: 0 0 10px 0;">Heavy-Duty Laser Printer</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px; flex-grow: 1;">High-yield monochrome laser printer ideal for large volume corporate document processing.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.4rem; font-weight: bold; color: #002d62;">Ksh 75,000</span>
                        <button style="background: #ff7300; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer;">Add to Cart</button>
                    </div>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #002d62; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,45,98,0.1); display: flex; flex-direction: column; position: relative;">
                <div style="position: absolute; top: 15px; right: -30px; background: #ff7300; color: white; padding: 5px 35px; transform: rotate(45deg); font-size: 0.8rem; font-weight: bold; z-index: 1;">SERVICE</div>
                <div style="background: #f0f4f8; height: 200px; display: flex; align-items: center; justify-content: center; color: #002d62;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M.102 2.223A.5.5 0 0 0 0 2.6v1.4a.5.5 0 0 0 .5.5h.586l1.114 4.457A.5.5 0 0 0 2.685 9.3h9.63a.5.5 0 0 0 .485-.36l1.114-4.457h.586a.5.5 0 0 0 .5-.5V2.6a.5.5 0 0 0-.102-.377L14.4.223A.5.5 0 0 0 14 0H2a.5.5 0 0 0-.4.223z"/>
                    </svg>
                </div>
                <div style="padding: 25px; display: flex; flex-direction: column; flex-grow: 1;">
                    <span style="color: #002d62; font-size: 0.85rem; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Repairs</span>
                    <h3 style="color: #002d62; font-size: 1.25rem; margin: 0 0 10px 0;">Diagnostic & Repair Booking</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px; flex-grow: 1;">Book a certified technician to diagnose and repair failing workstation hardware or servers.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-size: 0.8rem; color: #64748b;">Starting at</span>
                            <span style="font-size: 1.4rem; font-weight: bold; color: #002d62;">Ksh 10,500</span>
                        </div>
                        <button style="background: #002d62; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer;">Book Now</button>
                    </div>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #002d62; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,45,98,0.1); display: flex; flex-direction: column; position: relative;">
                <div style="position: absolute; top: 15px; right: -30px; background: #ff7300; color: white; padding: 5px 35px; transform: rotate(45deg); font-size: 0.8rem; font-weight: bold; z-index: 1;">PACKAGE</div>
                <div style="background: #f0f4f8; height: 200px; display: flex; align-items: center; justify-content: center; color: #002d62;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m.5 5a.5.5 0 0 1 1 0v3.25l2.75 1.65a.5.5 0 0 1-.5.86l-3-1.8A.5.5 0 0 1 8 8.5z"/>
                    </svg>
                </div>
                <div style="padding: 25px; display: flex; flex-direction: column; flex-grow: 1;">
                    <span style="color: #002d62; font-size: 0.85rem; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Management</span>
                    <h3 style="color: #002d62; font-size: 1.25rem; margin: 0 0 10px 0;">Monthly Cloud Support</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px; flex-grow: 1;">Ongoing 24/7 monitoring, security patching, and automated backups for your cloud infrastructure.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-size: 0.8rem; color: #64748b;">Per Month</span>
                            <span style="font-size: 1.4rem; font-weight: bold; color: #002d62;">Ksh 30,000</span>
                        </div>
                        <button style="background: #002d62; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer;">Subscribe</button>
                    </div>
                </div>
            </div>

        </div>
    </section>

</main>

<?php
// 3. Inject global footer components
include_once 'includes/footer.php';
?>