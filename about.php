<?php
// 1. Core configurations and database integration
require_once 'includes/db_connect.php';

// 2. Inject global site header navigation
include_once 'includes/header.php';
?>

<main style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #ffffff; padding-bottom: 80px;">

    <section style="background: linear-gradient(135deg, #002d62 0%, #001f42 100%); color: white; padding: 80px 20px; text-align: center;">
        <div style="max-width: 900px; margin: 0 auto;">
            <h1 style="font-size: 3rem; font-weight: 700; color:#e6f0fa; margin-bottom: 20px; letter-spacing: -0.5px;">About Our Organization</h1>
            <p style="font-size: 1.25rem; color: #cbd5e1; line-height: 1.6; max-width: 700px; margin: 0 auto;">
                Discover the engineering force and technical mindset safeguarding enterprise network operations and cloud architecture across the region.
            </p>
        </div>
    </section>

    <section style="padding: 60px 20px; max-width: 1100px; margin: 0 auto;">
        <div style="display: flex; flex-wrap: wrap; gap: 40px; align-items: center;">
            <div style="flex: 1; min-width: 300px;">
                <span style="color: #ff7300; font-weight: bold; text-transform: uppercase; tracking-spacing: 1px; font-size: 0.9rem;">Our Beginnings</span>
                <h2 style="color: #002d62; font-size: 2.2rem; margin: 10px 0 20px 0;">The Genesis of Blue Edge Solutions</h2>
                <p style="color: #475569; line-height: 1.7; font-size: 1.05rem; margin-bottom: 15px;">
                    Founded to bridge critical gaps in corporate infrastructure, Blue Edge Solutions Limited emerged from a collective tactical mission: transforming volatile legacy frameworks into unshakeable digital environments. 
                </p>
                <p style="color: #475569; line-height: 1.7; font-size: 1.05rem;">
                    What began as an agile consultancy assessing basic systems quickly transitioned into a major deployment partner specializing in iron-clad private clouds, defensive data routing protocols, and automated platform configurations.
                </p>
            </div>
            <div style="flex: 1; min-width: 300px; background: #f8fafc; padding: 40px; border-left: 5px solid #002d62; border-radius: 4px;">
                <h3 style="color: #002d62; margin-top: 0;">Did You Know?</h3>
                <p style="color: #64748b; font-style: italic; line-height: 1.6;">
                    "We engineered our very first containment network environment inside a localized data grid loop back in 2022. Today, we handle architectural assets managing data transactions across highly secure pipelines daily."
                </p>
            </div>
        </div>
    </section>

    <hr style="border: 0; height: 1px; background: #e2e8f0; max-width: 1100px; margin: 20px auto;">

    <section style="padding: 40px 20px; max-width: 1100px; margin: 0 auto;">
        <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
            
            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 40px; border-radius: 8px; flex: 1; min-width: 320px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: transform 0.2s;">
                <div style="background: #fff3e6; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                    <span style="color: #ff7300; font-size: 1.5rem; font-weight: bold;">M</span>
                </div>
                <h3 style="color: #002d62; font-size: 1.5rem; margin-bottom: 15px;">Our Tactical Mission</h3>
                <p style="color: #475569; line-height: 1.6; font-size: 1rem;">
                    To architect, implement, and maintain premium-tier technology infrastructures that eliminate operational vulnerabilities, optimizing system productivity for enterprise-scale industries worldwide.
                </p>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 40px; border-radius: 8px; flex: 1; min-width: 320px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: transform 0.2s;">
                <div style="background: #e6f0fa; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                    <span style="color: #002d62; font-size: 1.5rem; font-weight: bold;">V</span>
                </div>
                <h3 style="color: #002d62; font-size: 1.5rem; margin-bottom: 15px;">Our Global Vision</h3>
                <p style="color: #475569; line-height: 1.6; font-size: 1rem;">
                    To stand as the definitive architectural benchmark for corporate digital ecosystems, engineering absolute network defenses and advanced cloud mobility frameworks across emerging economic markets.
                </p>
            </div>

        </div>
    </section>

    <hr style="border: 0; height: 1px; background: #e2e8f0; max-width: 1100px; margin: 20px auto;">

    <section style="padding: 60px 20px; max-width: 1100px; margin: 0 auto; text-align: center;">
        <div style="max-width: 800px; margin: 0 auto;">
            <span style="color: #ff7300; font-weight: bold; text-transform: uppercase; font-size: 0.9rem;">Modern Scaling</span>
            <h2 style="color: #002d62; font-size: 2.2rem; margin: 10px 0 20px 0;">Our Evolutionary Footprint</h2>
            <p style="color: #475569; line-height: 1.8; font-size: 1.1rem; margin-bottom: 25px;">
                Today, Blue Edge Solutions Limited operates as an integrated solutions powerhouse. We don't just solve runtime errors; we conceptualize high-availability structural grids. By combining enterprise hardware systems with state-of-the-art preventative threat mechanics, we ensure our clients retain absolute system up-time under extreme transactional loads.
            </p>
            <p style="color: #475569; line-height: 1.8; font-size: 1.1rem;">
                From localized small firms upgrading to automated workflows to conglomerate entities requesting massive scale architecture migrations, our blueprints remain built upon integrity, resilience, and uncompromised digital clarity.
            </p>
        </div>
    </section>

</main>



<?php
// 3. Inject global footer copyright components 
include_once 'includes/footer.php';
?>