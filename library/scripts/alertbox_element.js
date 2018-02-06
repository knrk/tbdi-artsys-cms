/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/scripts
 */

/* --- class Art_AlertBox_Element --------------------------------------------------- */
/**
 *  Alertbox html element
 *  
 *  @param {string} [selector]
 *  @param {number} [status=1] 1 - OK, 2 - Alert, 3 - Error
 *  @returns {Art_AlertBox_Element}
 */
function Art_AlertBox_Element( selector, status )
{
    if( isUndef(selector) || null === selector )
    {
	//Create alertbox element
	this.element = document.createElement('div');
    }
    else
    {
	//Use selector
	this.element = document.querySelector( selector );
    }

    //Default status 1
    this.status = undefVal( parseInt(status), 1 );
    if( this.status > 3 || this.status < 1 )
    {
	this.status = 1;
    }
    
    //Disable auto hide for error messages
    if( this.status === 3 )
    {
	this.auto_hide = false;
    }

    addClass(this.element, Art_AlertBox_Element.classname);
    addClass(this.element, Art_AlertBox_Element.status_class[this.status]);
    
    //Create inner element
    this.element_inner = document.createElement('div');
    this.element_inner.className = Art_AlertBox_Element.class_inner;
    
    //Create close button
    this.element_close_button = document.createElement('div');
    this.element_close_button.className = Art_AlertBox_Element.close_button_class;
    
    //Create message box
    this.element_message_box = document.createElement('div');
    this.element_message_box.className = Art_AlertBox_Element.message_box_class;
    
    //Append to root element
    this.element.appendChild(this.element_inner);
    this.element_inner.appendChild(this.element_close_button);
    this.element_inner.appendChild(this.element_message_box);
}

/**
 *  Show alert message
 *  
 *  @param {(string|array)} messages
 *  @param {bool} [auto_hide=true] If false, alertbox will not hide, and must be hidden by user
 *  @returns {Art_AlertBox_Element}
 */
Art_AlertBox_Element.prototype.show = function( messages, auto_hide )
{
    var that = this;
    
    //Convert string to array
    if( typeof messages === 'string' )
    {
	messages = [messages];
    }
    else if( typeof messages === 'undefined' )
    {
	messages = [];
    }
    
    //Default autohide is true
    if( typeof auto_hide !== 'undefined' )
    {
	this.auto_hide = auto_hide;
    }

    //If message not empty
    if( null !== this.element && messages.length > 0 )
    {
	var alertMessage = '';
	
	//Assemble messages
	for( var i = 0; i < messages.length; i++)
	{
	    alertMessage = alertMessage + '<div class="'+Art_AlertBox_Element.message_item_class+'">'+messages[i]+'</div>' + "\n";
	}
	//Put message in	
	that.element_message_box.innerHTML = alertMessage;
	
	//Initial delay
	setTimeout(function() {
	    //Show alert
	    addClass( that.element, Art_AlertBox_Element.item_opened_class );
	    that.element.style.height = that.element.offsetHeight + "px";
	    
	    if( that.auto_hide )
	    {
		//Hide element after delay
		that.timeout_pre = setTimeout(function() {
		    that.close();
		}, 500 * Art_AlertBox_Element.animation_length);
	    }
	},10 * Art_AlertBox_Element.animation_length);
	
	
	//Add onclick handler
	that.element_close_button.addEventListener('click', function() {
	    that.close();
	});
    }
    
    return this;
};


/**
 *  Close alertbox element
 *  
 *  @retun {Art_AlertBox_Element}
 */
Art_AlertBox_Element.prototype.close = function()
{
    if( this.isOpened() )
    {
	var that = this;

	removeClass( that.element, Art_AlertBox_Element.item_opened_class );
	that.element.style.height = "";
	that.timeout_after = setTimeout(function(){
	    if( null !== that.element )
	    {
		that.element.parentNode.removeChild( that.element );
	    }
	}, 100 * Art_AlertBox_Element.animation_length);
    }
    
    return this;
};


/**
 *  @retun {bool} True if box is opened
 */
Art_AlertBox_Element.prototype.isOpened = function()
{
    return hasClass( this.element, Art_AlertBox_Element.item_opened_class );
};


/**
 *  @type Element 
 */
Art_AlertBox_Element.prototype.element = null;
/**
 *  @type Element 
 */
Art_AlertBox_Element.element_inner = null;
/**
 *  @type Element 
 */
Art_AlertBox_Element.element_close_button = null;
/**
 *  @type Element 
 */
Art_AlertBox_Element.element_message_box = null;
Art_AlertBox_Element.prototype.timeout_pre = null;
Art_AlertBox_Element.prototype.timeout_after = null;
Art_AlertBox_Element.prototype.auto_hide = true;
Art_AlertBox_Element.prototype.status = 1;

Art_AlertBox_Element.classname = 'art-alertbox-element';
Art_AlertBox_Element.class_inner = 'art-alertbox-element-inner';
Art_AlertBox_Element.item_opened_class = 'art-alertbox-element-opened';
Art_AlertBox_Element.message_item_class = 'art-alertbox-element-message-item';
Art_AlertBox_Element.close_button_class = 'art-alertbox-element-close-button';
Art_AlertBox_Element.message_box_class = 'art-alertbox-element-message-box';
Art_AlertBox_Element.status_class = { 1 : 'art-alertbox-element-ok', 2 : 'art-alertbox-element-alert', 3 : 'art-alertbox-element-error' };
Art_AlertBox_Element.animation_length = 10;