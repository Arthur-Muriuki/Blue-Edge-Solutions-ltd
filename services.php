<?php
// 1. Core configurations and database integration
require_once 'includes/db_connect.php';

// 2. Inject global site header navigation
include_once 'includes/header.php';
?>

<main style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; padding-bottom: 80px;">

    <section style="background: linear-gradient(135deg, #002d62 0%, #001f42 100%); color: white; padding: 80px 20px; text-align: center;">
        <div style="max-width: 900px; margin: 0 auto;">
            <span style="color: #ff7300; font-weight: bold; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1px;">Our Expertise</span>
            <h1 style="font-size: 3rem; font-weight: 700; color:#e2e8f0; margin: 10px 0 20px 0; letter-spacing: -0.5px;">Enterprise IT Solutions & Services</h1>
            <p style="font-size: 1.25rem; color: #cbd5e1; line-height: 1.6; max-width: 700px; margin: 0 auto;">
                We design, deploy, and maintain robust technical systems engineered to keep your business secure, scalable, and constantly operational.
            </p>
        </div>
    </section>

    <section style="padding: 60px 20px; max-width: 1200px; margin: 0 auto;">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 30px;">

            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 35px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="color: #ff7300; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                        </svg>
                    </div>
                    <h3 style="color: #002d62; font-size: 1.4rem; margin: 0 0 12px 0;">Computer User Support</h3>
                    <p style="color: #475569; line-height: 1.6; font-size: 0.95rem; margin: 0;">
                        Comprehensive helpdesk operations providing rapid OS troubleshooting, software provisioning, and end-user performance tuning to eliminate workforce downtime.
                    </p>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 35px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="color: #002d62; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m.5 4.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M5 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M5.5 10a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3m6 1a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m.5 4.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M4.5 5.013a1.5 1.5 0 0 0-1.37.89l-.91 2.125a1.5 1.5 0 0 0 .14 1.536l1.37 2.056a1.5 1.5 0 0 0 1.23.68h6.08a1.5 1.5 0 0 0 1.23-.68l1.37-2.056a1.5 1.5 0 0 0 .14-1.536l-.91-2.124a1.5 1.5 0 0 0-1.37-.89z"/>
                        </svg>
                    </div>
                    <h3 style="color: #002d62; font-size: 1.4rem; margin: 0 0 12px 0;">Networking Infrastructure</h3>
                    <p style="color: #475569; line-height: 1.6; font-size: 0.95rem; margin: 0;">
                        Architecture and deployment of high-availability LAN/WAN environments, managed routing/switching topologies, and structured core data cabling matrices.
                    </p>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 35px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="color: #ff7300; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M1.333 2.667C1.333 1.194 4.318 0 8 0s6.667 1.194 6.667 2.667V4c0 1.473-2.985 2.667-6.667 2.667S1.333 5.473 1.333 4z"/>
                            <path d="M14.667 5.667V7c0 1.473-2.985 2.667-6.667 2.667S1.333 8.473 1.333 7V5.667C2.41 6.456 4.982 7 8 7s5.59-.544 6.667-1.333m0 3.333V10.333c0 1.473-2.985 2.667-6.667 2.667S1.333 11.806 1.333 10.333V9c1.077.789 3.649 1.333 8 1.333s5.59-.544 6.667-1.333M1.333 12v1.333C1.333 14.806 4.318 16 8 16s6.667-1.194 6.667-2.667V12c-1.077.789-3.649 1.333-8 1.333s5.59-.544 6.667-1.333"/>
                        </svg>
                    </div>
                    <h3 style="color: #002d62; font-size: 1.4rem; margin: 0 0 12px 0;">Server Infrastructure</h3>
                    <p style="color: #475569; line-height: 1.6; font-size: 0.95rem; margin: 0;">
                        Provisioning and optimization of bare-metal and hypervised server systems, directory services (Active Directory), file assets, and localized enterprise database arrays.
                    </p>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 35px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="color: #002d62; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.5 1a.5.5 0 0 1 .5.5v13a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 .5-.5zM5 2v12h6V2z"/>
                            <path d="M6 3.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m0 3a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m0 3a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m0 3a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5"/>
                        </svg>
                    </div>
                    <h3 style="color: #002d62; font-size: 1.4rem; margin: 0 0 12px 0;">Data Center Administration</h3>
                    <p style="color: #475569; line-height: 1.6; font-size: 0.95rem; margin: 0;">
                        Supervising server rack deployments, physical space consolidation, baseline cooling performance, disaster recovery planning, and hot/cold aisle hardware arrangements.
                    </p>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 35px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="color: #ff7300; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M13.405 7.027a5.001 5.001 0 0 0-9.499-1.004A3.5 3.5 0 0 0 3.5 13H13a3 3 0 0 0 .405-5.973zM5.5 5.5a4 4 0 0 1 7.928.89A2.5 2.5 0 0 1 13 12H3.5a2.5 2.5 0 0 1-.5-4.95 4.004 4.004 0 0 1 2.5-1.55z"/>
                        </svg>
                    </div>
                    <h3 style="color: #002d62; font-size: 1.4rem; margin: 0 0 12px 0;">Cloud Server Management</h3>
                    <p style="color: #475569; line-height: 1.6; font-size: 0.95rem; margin: 0;">
                        Engineering and monitoring flexible workflows within AWS, Microsoft Azure, or custom private clouds. Includes load balancing and continuous architectural auditing.
                    </p>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 35px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="color: #002d62; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741zM1 11.105l4.708-2.897L1 5.383z"/>
                        </svg>
                    </div>
                    <h3 style="color: #002d62; font-size: 1.4rem; margin: 0 0 12px 0;">Mail Server Support</h3>
                    <p style="color: #475569; line-height: 1.6; font-size: 0.95rem; margin: 0;">
                        Administration of Microsoft 365, Google Workspace, and IMAP servers. Active implementation of MX routing, SPF alignment, DKIM cryptographic security, and DMARC enforcement.
                    </p>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 35px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="color: #ff7300; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M.102 2.223A.5.5 0 0 0 0 2.6v1.4a.5.5 0 0 0 .5.5h.586l1.114 4.457A.5.5 0 0 0 2.685 9.3h9.63a.5.5 0 0 0 .485-.36l1.114-4.457h.586a.5.5 0 0 0 .5-.5V2.6a.5.5 0 0 0-.102-.377L14.4.223A.5.5 0 0 0 14 0H2a.5.5 0 0 0-.4.223L.102 2.223zM2.5 1h11l.8 1H1.7l.8-1zM1 3h14v1H1V3zm2.394 5.3 1-4h7.212l1 4H3.394zM0 11.5a.5.5 0 0 1 .5-.5h15a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5H.5a.5.5 0 0 1-.5-.5v-3zm1 1v2h14v-2H1z"/>
                        </svg>
                    </div>
                    <h3 style="color: #002d62; font-size: 1.4rem; margin: 0 0 12px 0;">Hardware Repairs</h3>
                    <p style="color: #475569; line-height: 1.6; font-size: 0.95rem; margin: 0;">
                        Diagnostic procedures, component swaps, component sourcing, server fan assemblies, storage module tracking, and structural field repairs for workplace machines.
                    </p>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 35px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="color: #002d62; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M5.5 7a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0z"/>
                            <path d="M8 .5c-.662 0-1.77.249-2.813.525a61.11 61.11 0 0 0-2.772.815 1.454 1.454 0 0 0-1.003 1.184c-.573 4.197.756 7.307 2.368 9.365a11.191 11.191 0 0 0 2.417 2.3c.395.294.848.54 1.31.714a1.007 1.007 0 0 0 .592 0c.462-.174.914-.42 1.31-.714a11.192 11.192 0 0 0 2.416-2.3c1.612-2.058 2.94-5.168 2.368-9.365a1.454 1.454 0 0 0-1.003-1.184 61.09 61.09 0 0 0-2.772-.815C9.77.749 8.663.5 8 .5zm0 1.03c.553 0 1.608.23 2.622.496.953.25 1.956.544 2.544.71a.454.454 0 0 1 .313.37c.484 3.535-.558 6.177-1.996 8.013a10.197 10.197 0 0 1-2.193 2.09c-.31.23-.663.438-.99.563a.883.883 0 0 1-.6 0c-.327-.125-.68-.333-.99-.563a10.195 10.195 0 0 1-2.194-2.09C2.81 10.743 1.77 8.1 2.253 4.566a.454.454 0 0 1 .313-.37c.588-.165 1.59-.46 2.544-.71 1.014-.266 2.069-.497 2.622-.497z"/>
                        </svg>
                    </div>
                    <h3 style="color: #002d62; font-size: 1.4rem; margin: 0 0 12px 0;">Cybersecurity</h3>
                    <p style="color: #475569; line-height: 1.6; font-size: 0.95rem; margin: 0;">
                        Implementation of perimeter next-gen firewalls, zero-trust network access (ZTNA), proactive endpoint threat hunting, patch execution cycles, and immediate incident mitigation plans.
                    </p>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 35px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="color: #ff7300; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                        </svg>
                    </div>
                    <h3 style="color: #002d62; font-size: 1.4rem; margin: 0 0 12px 0;">Infrastructure Consultation</h3>
                    <p style="color: #475569; line-height: 1.6; font-size: 0.95rem; margin: 0;">
                        Strategic architectural reviews, hardware capacity lifecycle scaling plans, regulatory technological compliance auditing, and custom digital transformation mapping.
                    </p>
                </div>
            </div>

        </div>
    </section>

</main>

<?php
// 3. Inject global footer components (this includes your floating WhatsApp layout)
include_once 'includes/footer.php';
?>