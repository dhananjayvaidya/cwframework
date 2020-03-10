<?php 
require "http/models/Client.php";
require "http/models/Project.php";
require "http/models/ProjectCategory.php";
require "http/models/Service.php";
require "http/models/Team.php";
require "http/models/JobProfile.php";
class page extends Controller{
    function __construct(){
        parent::__construct();

    }
    public function startups(){
        $this->view->title = "Startups | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title" => "Startups",
            "items" => array(
                array(
                    "name" => "Home",
                    "link" => _PATH_
                ),
                array(
                    "name" => "Startups",
                    "link" => _PATH_."startups"
                )
            )
        );
        $this->view->render("front/page/startups");
    }
    public function brand_guidelines(){
        $this->view->title = "Brand Guidelines | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title" => "Brand Guidelines",
            "items" => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Brand Guidelines",
                    "link"  => _PATH_."brand-guidelines"
                )
            )
        );
        $this->view->render("front/page/brand_guidelines");
    }
    public function terms_and_conditions(){
        $this->view->title = "Terms & Conditions | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title" => "Terms & Conditions",
            "items" => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Terms & Conditions",
                    "link"  => _PATH_."terms-and-conditions"
                )
            )
        );
        $this->view->render("front/page/terms_and_conditions");
    }
    public function privacy_policy(){
        $this->view->title = "Privacy Policy | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title" => "Privacy Policy",
            "items" => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Privacy Policy",
                    "link"  => _PATH_."privacy-policy"
                )
            )
        );
        $this->view->render("front/page/privacy_policy");
    }
    public function payment_options(){
        $this->view->title = "Payment Options | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title" => "Payment Options",
            "items" => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Payment Options",
                    "link"  => _PATH_."payment-options"
                )
            )
        );
        $this->view->render("front/page/payment_options");
    }
    public function portfolio(){
        $this->view->title = "Our Portfolio | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title" => "Our Portfolio",
            "items" => array(
                array(
                    "name" => "Home",
                    "link" => _PATH_
                ),
                array(
                    "name" => "Portfolio",
                    "link" => _PATH_."portfolio"
                )
            )
        );

        $this->view->render("front/page/portfolio");
    }
    public function website_development(){
        $this->view->title = "#1 Website Development Company in Dombivli | Starting from ₹ 5000 | Dynamic Website | Shopping Website | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title" => "Website Development",
            "items" => array(
                array(
                    "name" => "Home",
                    "link" => _PATH_
                ),
                array(
                    "name" => "Services",
                    "link" => _PATH_."services"
                ),
                array(
                    "name" => "Website Development",
                    "link" => _PATH_."website-development"
                )
            )
        );

        $this->view->render("front/page/website_development");
    }
    public function graphic_designing(){
        $this->view->title = "Best Graphic Designing Company in Dombivli | Social Media Post Designing | Letter head |  Visiting Cards | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title" => "Graphic Designing",
            "items" => array(
                array(
                    "name" => "Home",
                    "link" => _PATH_
                ),
                array(
                    "name" => "Services",
                    "link" => _PATH_."services"
                ),
                array(
                    "name" => "Graphic Designing",
                    "link" => _PATH_."graphic-designing"
                )
            )
        );

        $this->view->render("front/page/graphic_designing");
    }
    public function e_commerce_website(){
        $this->view->title = "No. 1 eCommerce Website Development Company in Dombivli, Thane  | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title" => "eCommerce Website",
            "items" => array(
                array(
                    "name" => "Home",
                    "link" => _PATH_
                ),
                array(
                    "name" => "Services",
                    "link" => _PATH_."services"
                ),
                array(
                    "name" => "eCommerce Website",
                    "link" => _PATH_."e-commerce-website"
                )
            )
        );

        $this->view->render("front/page/e_commerce_website");
    }
    public function digital_marketing(){
        $this->view->title = "#1 Digital Marketing Agency in Dombivli | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title" => "Digital Marketing",
            "items" => array(
                array(
                    "name" => "Home",
                    "link" => _PATH_
                ),
                array(
                    "name" => "Services",
                    "link" => _PATH_."services"
                ),
                array(
                    "name" => "Digital Marketing",
                    "link" => _PATH_."digital-marketing"
                )
            )
        );

        $this->view->render("front/page/digital_marketing");
    }
    public function mobile_apps(){
        $this->view->title = "#1 Mobile App Development Company in DOmbivli | React Native | PhoneGap | Flutter | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title" => "Mobile Apps",
            "items" => array(
                array(
                    "name" => "Home",
                    "link" => _PATH_
                ),
                array(
                    "name" => "Services",
                    "link" => _PATH_."services"
                ),
                array(
                    "name" => "Mobile Apps",
                    "link" => _PATH_."mobile-apps"
                )
            )
        );

        $this->view->render("front/page/mobile_apps");
    }
    public function about_management(){
        $this->view->title = "About Management | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title"     => "About Management",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "About",
                    "link"  => _PATH_."about-us"
                ),
                array(
                    "name"  => "Management",
                    "link"  => _PATH_."about/management"
                )
            ),
        );
        $this->view->render("front/page/about_management");
    }
    public function about_achievements(){
        $this->view->title = "About Achievements | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title"     => "About Achievements",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "About",
                    "link"  => _PATH_."about-us"
                ),
                array(
                    "name"  => "Achievements",
                    "link"  => _PATH_."about/achievements"
                )
            ),
        );
        $this->view->render("front/page/about_achievements");
    }
    public function about_employee_engagement(){
        $this->view->title = "Employee Engagement | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title"     => "Employee Engagement",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "About",
                    "link"  => _PATH_."about-us"
                ),
                array(
                    "name"  => "Employee Engagement",
                    "link"  => _PATH_."about/employee-engagement"
                )
            ),
        );
        $this->view->render("front/page/about_employee_engagement");
    }
    public function about_careers(){
        $this->view->title = "Careers | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title"     => "Careers",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Careers",
                    "link"  => _PATH_."about/careers"
                )
            ),
        );
        $CareerObj = new JobProfileModel();
        $CareerData = $CareerObj->getAll();
        $this->view->data = $CareerData;
        $this->view->render("front/page/about_careers");
    }
     public function channel_registration(){
        $ServiceObj = new ServiceModel();
        $d = $ServiceObj->getAll();
        $this->view->serviceData =$d;
        
        $this->view->breadcrumb = array(
            "title"     => "Channel Registration",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Channel Registration",
                    "link"  => _PATH_."channel_registration"
                )
            ),
        );
        $this->view->title = "Channel Registration |  "._COMPANY_NAME_;
        $this->view->render("front/page/channel_registration");
    }
    public function collection_registration(){
        $ServiceObj = new ServiceModel();
        $d = $ServiceObj->getAll();
        $this->view->serviceData =$d;
        
        $this->view->breadcrumb = array(
            "title"     => "Collection Registration",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Collection Registration",
                    "link"  => _PATH_."collection_registration"
                )
            ),
        );
        $this->view->title = "Collection Registration |  "._COMPANY_NAME_;
        $this->view->render("front/page/collection_registration");
    }
    public function recycler_registration(){
        $ServiceObj = new ServiceModel();
        $d = $ServiceObj->getAll();
        $this->view->serviceData =$d;
        
        $this->view->breadcrumb = array(
            "title"     => "Recycler Registration",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Recycler Registration",
                    "link"  => _PATH_."recycler_registration"
                )
            ),
        );
        $this->view->title = "Recycler Registration |  "._COMPANY_NAME_;
        $this->view->render("front/page/recycler_registration");
    }
    public function projects(){
        $ProjectCategoryObj = new ProjectCategoryModel();
        $ProjectObj = new ProjectModel();
        $d = $ProjectObj->getAll();
        $this->view->title = "Our Projects | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title"     => "Our Projects",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Projects",
                    "link"  => _PATH_."projects"
                )
            ),
        );
        $this->view->projectData = $ProjectCategoryObj->getAll();
        $this->view->projects    = $d;
        $this->view->render("front/page/projects");
    }
    public function services(){
        $ServiceObj = new ServiceModel();
        $d = $ServiceObj->getAll();
        $this->view->title = "Our Services | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title"     => "Our Services",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Services",
                    "link"  => _PATH_."services"
                )
            ),
        );
        $this->view->serviceData =$d;
        $this->view->render("front/page/services");
    }
    public function company_policies(){
        $this->view->title = "Our Company Policies | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title"     => "Company Policies",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Company Policies",
                    "link"  => _PATH_."company-policies"
                )
            ),
        );
        $this->view->render("front/page/company_policies");
    }
    public function clients(){
        $ClientObj = new ClientModel();
        $this->view->title = "Our Clients | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title"     => "Our Clients",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Clients",
                    "link"  => _PATH_."clients"
                )
            ),
        );
        $this->view->data = $ClientObj->getAll();
        $this->view->render("front/page/clients");
    }
    public function register(){
        $ServiceObj = new ServiceModel();
        $d = $ServiceObj->getAll();
        $this->view->serviceData =$d;
        
        $this->view->breadcrumb = array(
            "title"     => "Register",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Register",
                    "link"  => _PATH_."register"
                )
            ),
        );
        $this->view->title = "Register |  "._COMPANY_NAME_;
        $this->view->render("front/page/register");
    }
    public function login(){
        $ServiceObj = new ServiceModel();
        $d = $ServiceObj->getAll();
        $this->view->serviceData =$d;
        
        $this->view->breadcrumb = array(
            "title"     => "Login",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Login",
                    "link"  => _PATH_."login"
                )
            ),
        );
        $this->view->title = "Login |  "._COMPANY_NAME_;
        $this->view->render("front/page/login");
    }
    public function contact_us(){
        $this->view->breadcrumb = array(
            "title"     => "Contact Us",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Contact Us",
                    "link"  => _PATH_."contact-us"
                )
            ),
        );
        $this->view->title = "Contact Us |  "._COMPANY_NAME_;
        $this->view->render("front/page/contact-us");
    }
    public function certification(){
        $this->view->title = "Certification | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title"     => "Certification",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "Certification",
                    "link"  => _PATH_."certification"
                )
            ),
        );
        $this->view->render("front/page/certification");
    }
    public function about_us(){
        $TeamObj = new TeamModel();
        $T = $TeamObj->getAll();
        $this->view->title = "About Us | "._COMPANY_NAME_;
        $this->view->breadcrumb = array(
            "title"     => "About Us",
            "items"     => array(
                array(
                    "name"  => "Home",
                    "link"  => _PATH_
                ),
                array(
                    "name"  => "About",
                    "link"  => _PATH_."about-us"
                )
            ),
        );
        $this->view->teamMember = $T;
        $this->view->title = "About Us | "._COMPANY_NAME_;
        $this->view->render("front/page/about-us");
    }
}
?>