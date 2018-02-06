/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/scripts
 */

/* --- anonymous class Art_AlertBox_Container extends Art_Component --------- */
var Art_AlertBox = new Art_Component();

/**
 *  @type Element 
 */
Art_AlertBox.element = null;
Art_AlertBox.boxes = [];
Art_AlertBox.classname = 'art-alertbox';

/**
 *  Initialize Alertbox
 *  
 *  @return {Art_AlertBox}
 */
Art_AlertBox.initialize = function() 
{
    if( !this.initialized && null === this.element )
    {
	//Create this.element
	this.element = document.createElement('div');
	
	//Add classname
	this.element.className = Art_AlertBox.classname;
	
	//Add to body
	if( document.body )
	{
	    document.body.insertBefore(this.element, document.body.childNodes[0]);
	}
	else
	{
	    console.log('Art_AlertBox can\'t be initialized from head script');
	}
	
	this.initialized = true;
    }
    
    return this;
};


/**
 *  Add new alert
 *  
 *  @param {(string|array)} messages
 *  @param {number} [status=1] 1 - OK, 2 - Alert, 3 - Error
 *  @param {bool} [auto_hide=true]
 *  @return {Art_AlertBox}
 */
Art_AlertBox.newAlert = function( messages, status, auto_hide  )
{
    this.initialize();
    
    //Create new box
    var box = new Art_AlertBox_Element(null, status);
    this.boxes.push(box);
    this.element.appendChild( box.element );
    
    box.show(messages, auto_hide);
    
    return this;
};


/**
 *  Add new alert - alias for newAlert() function
 *  
 *  @param {(string|array)} messages
 *  @param {number} [status=1] 1 - OK, 2 - Alert, 3 - Error
 *  @param {bool} [auto_hide=true]
 *  @return {Art_AlertBox}
 */
Art_AlertBox.show = function( messages, status, auto_hide  )
{
    return this.newAlert(messages, status, auto_hide );
};