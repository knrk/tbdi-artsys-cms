<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package extensions/tcpdf
 *  @uses TCPDF
 */
class Art_TCPDF extends TCPDF 
{
    var $htmlHeader;
	var $htmlHeaderMargin = 0;
	var $htmlFooterMargin = 0;
	
	/**
	 *	Get style used in footer
	 * 
	 *	@return string
	 */
	static function getFooterStyle()
	{
		return '<style>
				div {
					color:#555555;
					font-size: 10px;
					text-align: center;
				}

				a {
					color:#555555;
					text-decoration: none;
				}
				</style>';
	}
	
	/**
	 *	Get style used in header
	 * 
	 *	@return string
	 */
	static function getHeaderStyle()
	{
		return '<style>
				div {
					font-size: 15px;
					text-align: center;
				}
				</style>';
	}	
	
	/*
	 *	@see parent::setHtmlHeader()
	 */
    public function setHtmlHeader($htmlHeader,$margin_top=0) 
	{
        $this->htmlHeader = $htmlHeader;
        $this->htmlHeaderMargin = $margin_top;
    }
	
	
	/*
	 *	@see parent::setHtmlFooter()
	 */
    public function setHtmlFooter($htmlFooter,$margin_bottom=0)
    {
        $this->htmlFooter = $htmlFooter;
        $this->htmlFooterMargin = -$margin_bottom;
    }
    
	
	/*
	 *	@see parent::Header()
	 */
	public function Header() 
	{
		
        $this->SetFont(DEFAULT_FONT_FAMILY, '', DEFAULT_FONT_SIZE, '', true);
        $this->SetY($this->htmlHeaderMargin);
		$header = $this->getHeaderStyle().'<div>'.$this->htmlHeader.'</div>';
        $this->writeHTMLCell(0, 0, '', '', $header, 0, 1, 0, true);
	}

	
	/*
	 *	@see parent::Footer()
	 */
	public function Footer() 
	{
        $this->SetFont(DEFAULT_FONT_FAMILY, '', DEFAULT_FONT_SIZE, '', true);
        $this->SetY($this->htmlFooterMargin);
		$footer = $this->getFooterStyle().'<div>'.$this->htmlFooter.'</div>';
        $this->WriteHtmlCell(0, 0, '', '',$footer, 0, 1, 0, true, 'C');
	}
    
	
	/*
	 *	@see parent::Output()
	 */
    public function Output($name = 'output.pdf', $dest = 'F') 
	{
        parent::Output($name,$dest);
    }
	
	
	/*
	 *	@see parent::writeHTML()
	 */
    public function writeHTML($html, $ln = true, $fill = false, $reseth = true, $cell = false, $align = '') 
	{
        parent::writeHTML($html, $ln, $fill, $reseth, $cell, $align);
    }
}