/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/scripts
 */

/* --- class Art_Arrow_Down ------------------------------------------------- */
/**
 *  Arrow down HTML element
 *  When clicked, screen is scolled to the 
 *	position of scroll_to element (or by number of offset-top pixels)
 *  
 *  @requires jQuery
 *  @param {string} selector
 *  @param {(string|number)} scroll_to selector or number of pixels
 *  @param {(string|number)} [hide_since] Selector or number of pixels where arrow should be hidden
 *  @param {number} [scroll_offset=0]
 *  @param {number} [scroll_length=500]
 *  @param {type} [fade_length=150]
 *  @returns {Art_Arrow_Down}
 *  @example new Art_Arrow_Down('.arrow_down', '.scroll_to_content', 400, 200);
 */
function Art_Arrow_Down( selector, scroll_to, hide_since, scroll_offset, scroll_length, fade_length ) 
{
    if( arguments.length < 2 )
    {
	console.log(ERROR_NOT_ENOUGH_ARGUMENTS);
	return;
    }

    //Defaults
    this.selector = selector;
    this.scroll_offset = undefVal(scroll_offset, 0);
    this.scroll_length = undefVal(scroll_length, 500);
    this.fade_length = undefVal(fade_length, 150);

    //Get element
    this.element = document.querySelector(selector);
    
    //Save scroll_to
    switch( typeof scroll_to )
    {    
	//Get scroll_to element if string
	case 'string':
	{
	    this.scroll_to_element = document.querySelector(scroll_to);
	    break;
	}
	case 'number':
	{
	    this.scroll_to_pixels = scroll_to;
	    break;
	}
	default:
	{
	    console.log(ERROR_INVALID_ARGUMENTS);
	    return;
	}
    }
    
    //Save hide_since
    switch( typeof hide_since )
    {    
	//Get scroll_to element if string
	case 'string':
	{
	    this.hide_since_element = document.querySelector(hide_since);
	    break;
	}
	case 'number':
	{
	    this.hide_since_pixels = hide_since;
	    break;
	}
	default:
	{
	    console.log(ERROR_INVALID_ARGUMENTS);
	    return;
	}
    }    
    
    //Bind onclick event for arrow
    this.bindOnclick();
    this.bindHideOnScroll();    
}

/** 
 *  @type Element
 */
Art_Arrow_Down.prototype.element = null;
Art_Arrow_Down.prototype.selector = '';
Art_Arrow_Down.prototype.scroll_length = 0;
Art_Arrow_Down.prototype.fade_length = 0;
Art_Arrow_Down.prototype.scroll_to_element = null;
Art_Arrow_Down.prototype.scroll_to_pixels = -1;
Art_Arrow_Down.prototype.hide_since_element = null;
Art_Arrow_Down.prototype.hide_since_pixels = -1;

Art_Arrow_Down.classHidden = 'art-arrow-down-hidden';


/**
 *  Binds onclick event for elements matching input selector
 *  
 *  @returns {Art_Arrow_Down}
 */
Art_Arrow_Down.prototype.bindOnclick = function()
{
    //Dont bind if scroll to is not set
    if( this.scroll_to_pixels < 0 && null === this.scroll_to_element )
    {
	return;
    }
    
    var that = this;
    
    //Scroll down function bound to element
    var scrollDownFunc = function()
    {
	if( window.jQuery )
	{
	    if( null !== that.scroll_to_element )
	    {
		that.scroll_to_pixels = that.scroll_to_element.getBoundingClientRect().top + that.scroll_offset;
	    }
	    
	    window.$hb.stop().animate({
		scrollTop: that.scroll_to_pixels
	    }, that.scroll_length);
	}
	else
	{
	    console.log(ERROR_JQUERY_NOT_LOADED);
	}
    };
    
    //Bind event callback for element
    this.element.addEventListener('click',scrollDownFunc);
    
    return this;
};


/**
 *  Hides element when scrolled behind "hide_since" element
 *  
 *  @returns {Art_Arrow_Down}
 */
Art_Arrow_Down.prototype.bindHideOnScroll = function()
{
    //Dont bind if scroll to is not set
    if( this.hide_since_pixels < 0 && null === this.hide_since_element )
    {
	return;
    }    
    
    var that = this;
    
    if( this.hide_since_pixels > 0 )
    {
	var onScrollFunc = function() 
	{
	    if( window.pageYOffset > that.hide_since_pixels )
	    {
		if( !hasClass(that.element, Art_Arrow_Down.classHidden ) )
		{
		    addClass(that.element, Art_Arrow_Down.classHidden);
		}
	    }
	    else
	    {
		if( hasClass(that.element, Art_Arrow_Down.classHidden ) )
		{
		    removeClass(that.element, Art_Arrow_Down.classHidden);
		}
	    }
	};
    }
    else
    {
	var onScrollFunc = function() 
	{
	    if( window.pageYOffset > that.hide_since_element.getBoundingClientRect().top )
	    {
		if( !hasClass(that.element, Art_Arrow_Down.classHidden ) )
		{
		    addClass(that.element, Art_Arrow_Down.classHidden);
		}
	    }
	    else
	    {
		if( hasClass(that.element, Art_Arrow_Down.classHidden ) )
		{
		    removeClass(that.element, Art_Arrow_Down.classHidden);
		}
	    }
	};
    }
    
    window.addEventListener('scroll', onScrollFunc);
    
    return this;
};