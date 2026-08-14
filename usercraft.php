<?php
// 1. Core configurations and database integration
require_once 'includes/db_connect.php';

// Inject the global header navigation
$page_title = "UserCraft Consult | Premium ICT Support & Systems Integration";
include_once 'includes/header.php';
?>

<main style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f1f5f9; padding-bottom: 90px; color: #1e293b;">

    <!-- Brand Introduction Header Banner -->
    <header style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 60px 20px; text-align: center; border-bottom: 4px solid #ff7300;">
        <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; align-items: center;">
            
            <h1 style="font-size: 2.5rem; font-weight: 700; color: #f8fafc; margin: 0 0 10px 0; width: 100%;">Welcome to UserCraft Consult</h1>
            <p style="font-size: 1.35rem; font-weight: 600; color: #ff7300; line-height: 1.6; max-width: 650px; margin: 0 auto;">
                ICT Support & Systems Integration
            </p>
        </div>
    </header>

    <!-- Services Infrastructure Showcase -->
    <section style="padding: 60px 20px; max-width: 1200px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 50px;">
            <span style="color: #ff7300; font-weight: bold; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1.5px;">Service Scope</span>
            <h2 style="font-size: 2rem; color: #0f172a; margin-top: 5px;">Enterprise Systems Implementation & Maintenance</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;">
            
            <!-- Service Card 1: Point of Sale -->
            <div style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 40px 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="background: #eff6ff; color: #2563eb; width: 50px; height: 50px; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h11A1.5 1.5 0 0 1 15 2.5v10.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 13V2.5zM2.5 2a.5.5 0 0 0-.5.5v3.5h12V2.5a.5.5 0 0 0-.5-.5h-11zm12 5H2v6a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5V7z"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.35rem; color: #0f172a; margin-bottom: 12px; font-weight: 600;">Tailored POS Software</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
                        Complete rollout of custom retail and hospitality Point of Sale configurations. We calibrate checkout peripherals, inventory tracking rules, barcode modules, and local transaction printers matching your retail landscape.
                    </p>
                </div>
                <a href="#quote-form" style="display: block; text-align: center; background-color: #0f172a; color: white; text-decoration: none; padding: 12px; border-radius: 4px; font-weight: 600; font-size: 0.95rem; transition: background 0.2s;">Request POS Support</a>
            </div>

            <!-- Service Card 2: QuickBooks -->
            <div style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 40px 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="background: #f0fdf4; color: #16a34a; width: 50px; height: 50px; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2 2a2 2 0 0 1 2-2h8 a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H4z"/>
                            <path d="M5.5 4h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1zm0 2.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1zm0 2.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1z"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.35rem; color: #0f172a; margin-bottom: 12px; font-weight: 600;">QuickBooks Implementations</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
                        Expert framework deployment and multi-user environment setup for QuickBooks accounting suites. We handle ledger provisioning, automated server background backups, profile permissions, and troubleshooting connectivity issues.
                    </p>
                </div>
                <a href="#quote-form" style="display: block; text-align: center; background-color: #0f172a; color: white; text-decoration: none; padding: 12px; border-radius: 4px; font-weight: 600; font-size: 0.95rem; transition: background 0.2s;">Request QuickBooks Support</a>
            </div>

            <!-- Service Card 3: Sage Pastel -->
            <div style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 40px 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="background: #fff7ed; color: #ea580c; width: 50px; height: 50px; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm11.5 5.5a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h7zm0-2.5a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h7zm-7 5.5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7z"/>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.35rem; color: #0f172a; margin-bottom: 12px; font-weight: 600;">Sage Pastel ERP Support</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
                        Strategic architectural deployment and engineering maintenance for Sage Pastel systems. We resolve database parsing blockages, execute system version upgrades, manage network licensing allocation, and fix server sync delays.
                    </p>
                </div>
                <a href="#quote-form" style="display: block; text-align: center; background-color: #0f172a; color: white; text-decoration: none; padding: 12px; border-radius: 4px; font-weight: 600; font-size: 0.95rem; transition: background 0.2s;">Request Sage Support</a>
            </div>

        </div>
    </section>

    <!-- Interactive Custom Quote Formulation Section -->
    <section id="quote-form" style="padding: 40px 20px; max-width: 700px; margin: 40px auto 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.75rem; color: #0f172a; margin-bottom: 5px;">Inquire for System Support</h2>
            <p style="color: #64748b; font-size: 0.95rem;">Provide your parameters below to receive an operational engineering quote.</p>
        </div>

        <form action="#" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Your Name</label>
                    <input type="text" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Corporate Email</label>
                    <input type="email" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Target Software Ecosystem</label>
                <select required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background: white; box-sizing: border-box;">
                    <option value="">-- Choose Option --</option>
                    <option value="pos">Tailored Retail Point of Sale (POS)</option>
                    <option value="quickbooks">QuickBooks Accounting Environment</option>
                    <option value="sage">Sage Pastel ERP Software Suite</option>
                    <option value="general">General ICT Maintenance Contract</option>
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Operational Scope Details</label>
                <textarea rows="4" required placeholder="Describe your technical issues or deployment parameters..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; resize: vertical; box-sizing: border-box;"></textarea>
            </div>

            <button type="submit" style="background: #ff7300; color: white; border: none; padding: 14px; border-radius: 4px; font-weight: bold; font-size: 1rem; cursor: pointer; transition: background 0.2s;">
                Submit Technical Quote Request
            </button>
        </form>
    </section>

</main>

<?php
// Inject global footer components
include_once 'includes/footer.php';
?>