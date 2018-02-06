/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/scripts
 */

/* --- anonymous class Art_Email_Protection extends Art_Component --------- */
var Art_Email_Protection = new Art_Component();

/**
 *  @type NodeList 
 */
Art_Email_Protection.elements = null;
Art_Email_Protection.element_selector = '.art-email-protected';
Art_Email_Protection.name_postfix = 'n';
Art_Email_Protection.domain_postfix = 'd';
Art_Email_Protection.country_postfix = 'c';
Art_Email_Protection.mail_to_postfix = 'mail_to';

/**
 *  Initialize component
 * 
 *  @return {Art_Email_Protection}
 */
Art_Email_Protection.initialize = function()
{
    if( !this.initialized )
    {
	this.transcriptAll();
	this.initialized = true;
    }
    
    return this;
};

/**
 *  Transcript element - use with ArtSys helper function
 * 
 *  @return {Art_Email_Protection}
 */
Art_Email_Protection.transcriptElement = function( element ) 
{
    //Get email parts
    var name = element.getAttribute( 'data-' + this.name_postfix );
    var domain = element.getAttribute( 'data-' + this.domain_postfix );
    var country = element.getAttribute( 'data-' + this.country_postfix );

    //Assemble full email
    var full_mail = name + '@' + domain + '.' + country;

    //If mail should be wrapped in mail to href
    if( element.getAttribute('data-'+this.mail_to_postfix) === '1')
    {
	var content = document.createElement('a');
	content.innerHTML = full_mail;
	content.href = 'mailto:'+full_mail;
    }
    else
    {
	var content = document.createTextNode(full_mail);
    }

    //Replace node with new content
    element.parentNode.replaceChild(content, element);
    
    return this;
};

/**
 *  Transcript alle elements
 * 
 *  @return {Art_Email_Protection}
 */
Art_Email_Protection.transcriptAll = function() 
{
    this.elements = document.querySelectorAll( Art_Email_Protection.element_selector );
    
    for( var i = 0; i < this.elements.length; i++ )
    {
	Art_Email_Protection.transcriptElement( this.elements[i] );
    }
    
    return this;
};