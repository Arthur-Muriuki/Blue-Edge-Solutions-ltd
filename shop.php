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
                Upgrade your infrastructure with enterprise-grade networking equipment, premium consumables, or book our specialized IT service packages directly.
            </p>
        </div>
    </section>

    <section style="padding: 60px 20px; max-width: 1200px; margin: 0 auto;">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                <div style="background: #f8fafc; height: 220px; display: flex; align-items: center; justify-content: center; padding: 15px;">
                    <img src="assets/images/wifi 6 router.jpeg" alt="Enterprise Wi-Fi 6 Router" style="max-width: 100%; max-height: 100%; object-fit: contain;">
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
                <div style="background: #f8fafc; height: 220px; display: flex; align-items: center; justify-content: center; padding: 15px;">
                    <img src="assets/images/D-Link 24 Port Switch.jpeg" alt="24-Port Managed Switch" style="max-width: 100%; max-height: 100%; object-fit: contain;">
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
                <div style="background: #f8fafc; height: 220px; display: flex; align-items: center; justify-content: center; padding: 15px;">
                    <img src="assets/images/Heavy Duty Laser Printer.jpeg" alt="Heavy-Duty Laser Printer" style="max-width: 100%; max-height: 100%; object-fit: contain;">
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

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                <div style="background: #f8fafc; height: 220px; display: flex; align-items: center; justify-content: center; padding: 15px;">
                    <img src="assets/images/Toner cartridges.jpeg" alt="HP LaserJet Toner Cartridge" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
                <div style="padding: 25px; display: flex; flex-direction: column; flex-grow: 1;">
                    <span style="color: #ff7300; font-size: 0.85rem; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Consumables</span>
                    <h3 style="color: #002d62; font-size: 1.25rem; margin: 0 0 10px 0;">HP 85A Black LaserJet Toner</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px; flex-grow: 1;">Produces sharp black text and crisp graphics for critical business office print jobs.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.4rem; font-weight: bold; color: #002d62;">Ksh 8,500</span>
                        <button style="background: #ff7300; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer;">Add to Cart</button>
                    </div>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                <div style="background: #f8fafc; height: 220px; display: flex; align-items: center; justify-content: center; padding: 15px;">
                    <img src="assets/images/cyan toner.jpeg" alt="Premium Laser Toner Cartridge" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
                <div style="padding: 25px; display: flex; flex-direction: column; flex-grow: 1;">
                    <span style="color: #ff7300; font-size: 0.85rem; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Consumables</span>
                    <h3 style="color: #002d62; font-size: 1.25rem; margin: 0 0 10px 0;">CRG-054 Premium Cyan Toner</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px; flex-grow: 1;">High-capacity replacement color toner cartridge optimized for high-volume laser units.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.4rem; font-weight: bold; color: #002d62;">Ksh 9,200</span>
                        <button style="background: #ff7300; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer;">Add to Cart</button>
                    </div>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #002d62; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,45,98,0.1); display: flex; flex-direction: column; position: relative;">
                <div style="position: absolute; top: 15px; right: -30px; background: #ff7300; color: white; padding: 5px 35px; transform: rotate(45deg); font-size: 0.8rem; font-weight: bold; z-index: 1;">SERVICE</div>
                <div style="background: #f8fafc; height: 220px; display: flex; align-items: center; justify-content: center; padding: 15px;">
                    <img src="assets/images/Technical repair.jpeg" alt="Premium Laser Toner Cartridge" style="max-width: 100%; max-height: 100%; object-fit: contain;">
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
                <div style="background: #f8fafc; height: 220px; display: flex; align-items: center; justify-content: center; padding: 15px;">
                    <img src="assets/images/Cloud Support.jpeg" alt="Premium Laser Toner Cartridge" style="max-width: 100%; max-height: 100%; object-fit: contain;">
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