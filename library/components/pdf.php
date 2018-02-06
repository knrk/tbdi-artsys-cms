<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 *  @uses TCPDF External TCPDF library
 *  @link http://www.tcpdf.org
 *  @example
    $pdf = PDF::newFile();
    
    $pdf->SetTitle('Title');
    $pdf->SetSubject('Subject');
    $pdf->SetKeywords('keywords space separated');
    
    $pdf->setHtmlHeader('A header text',14);
    //$pdf->setHtmlFooter('A footer text',10);
    
    $pdf->AddPage();
    $pdf->writeHTML('<b>Body</b>');

    $pdf->Output('output.pdf');
 *
 *
 */
final class Art_PDF extends Art_Abstract_Component {
    
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
    
    
    /**
     *  Initialize the component
	 * 
     *  @static
     *  @return void
     */
    static function init()
    {
        if(parent::init())
        {
            require_once(ftest('extensions/tcpdf/external/tcpdf_include.php'));
            require_once(ftest('extensions/tcpdf/art_tcpdf.php'));
            
            define('DEFAULT_FONT_FAMILY', 'dejavusans');
            define('DEFAULT_FONT_SIZE', '10');
        }
    }
    	
    /**
     *  Create new PDF file
     *  @static
     *  @return Art_TCPDF PDF file instance
     */
    static function newFile()
    {
		//Lazy init
		static::init();
		
        //New instance
        $pdf = new Art_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        //Set author and creator from configuration file
        $pdf->SetCreator(Art_Register::in('pdf')->get('creator'));
        $pdf->SetAuthor(Art_Register::in('pdf')->get('author'));
        
        //Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        //Set view to 100%, allow scrolling
        $pdf->SetDisplayMode(100, 'continuous');
        
        //Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 22.5, PDF_MARGIN_RIGHT);
        
        //Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_LEFT);
        
        //Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        //Set default font subsetting mode
        $pdf->setFontSubsetting(true);
        
        //Set font
        $pdf->SetFont(DEFAULT_FONT_FAMILY, '',DEFAULT_FONT_SIZE, '', true);
        
		//Set default footer
		$pdf->setHtmlFooter('IT ART - Tvorba webových stránek | <a href="http://itart.cz">www.itart.cz</a>',10);
		
		//Set META
		$pdf->SetTitle(Art_Template::getMeta('title'));
		$pdf->SetSubject(Art_Template::getMeta('title'));
		$pdf->SetKeywords(Art_Template::getMeta('title'));
		
        return $pdf;
    }
}