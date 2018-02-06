/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/scripts
 */

/**
 *  Returns true if value is undefined
 *  
 *  @param {type} input_value
 *  @returns {Boolean}
 */
function isUndef( input_value )
{
    return typeof input_value === 'undefined';
}


/**
 *  Return default value if input value is undefined
 *  
 *  @param {mixed} input_value
 *  @param {mixed} default_value
 *  @returns {mixed}
 */
function undefVal( input_value, default_value )
{
    if( isUndef(input_value) )
    {
	return default_value;
    }
    else
    {
	return input_value;
    }
};


/**
 *  Return default value if input value is null
 *  
 *  @param {mixed} input_value
 *  @param {mixed} default_value
 *  @returns {mixed}
 */
function nullVal( input_value, default_value )
{
    if( null === input_value )
    {
	return default_value;
    }
    else
    {
	return input_value;
    }
};


/**
 * Dump data and exit the script
 * 
 * @param {mixed} data
 * @returns {undefined}
 */
function d( data )
{
    console.log(data);
    throw new Error('THIS ERROR IS USED FOR STOPPING THE SCIRPT');
}


/**
 * Console.log alias
 * 
 * @param {mixed} data
 * @returns {undefined}
 */
function p( data )
{
    console.log(data);
}


/**
 *  Returns true if element has class
 *  
 *  @param {Element} element
 *  @param {string} searched_class
 *  @returns {Boolean}
 */
function hasClass( element, searched_class )
{
    return (' ' + element.className + ' ').indexOf(' ' + searched_class + ' ') > -1;
};


/**
 *  Add class to Element class list
 *  
 *  @param {Element} element
 *  @param {string} class_name
 *  @returns {undefined}
 */
function addClass( element, class_name )
{
    if( !hasClass( element, class_name ) )
    {
	element.className = element.className + ' ' + class_name;
    }
};


/**
 *  Remove class from Element class list
 *  
 *  @param {Element} element
 *  @param {string} class_name
 *  @returns {undefined}
 */
function removeClass( element, class_name )
{
    if( hasClass( element, class_name ) )
    {
	var classes = element.className.split(' ');
	for( var i = 0; i < classes.length; i++ )
	{
	    if( classes[i] === class_name )
	    {
		classes.splice( i, 1 );
	    }
	}
	
	element.className = classes.join(' ');
    }
};


/**
 *  Toggle class in Element class list
 *  
 *  @param {Element} element
 *  @param {string} class_name
 *  @returns {undefined}
 */
function toggleClass( element, class_name )
{
    if( hasClass( element, class_name ) )
    {
	removeClass( element, class_name );
    }
    else
    {
	addClass( element, class_name );
    }
};


/**
 *  Inherit child class from base
 *  
 *  @param {function} child
 *  @param {function} base
 *  @returns {undefined}
 */
function inherit(child, base) 
{
    if( Object.create )
    {
	child.prototype = Object.create(base.prototype);
	child.prototype.constructor = child;	
    }
    else
    {
	child.prototype = new base;
	child.prototype.constructor = child;
    }
};




/**
 *  Generate random alphanumeric string by given length
 *  
 *  @param {int} length
 *  @returns {String}
 */
function rand_str( length )
{
    var output = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < length; i++ )
    {
        output += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    return output;
}


/**
 * Get array maximal value
 * 
 * @param {array} array
 * @returns {number}
 */
function array_max( array )
{
    return Math.max.apply(null, array);
}


/**
 * Get array minimal value
 * 
 * @param {array} array
 * @returns {number}
 */
function array_min( array )
{
    return Math.min.apply(null, array);
}


/**
 *  jQuery .toggleClass function
 */
if( window.jQuery )
{
    $.fn.extend({
       toggleClass : function( class_name ) {
	  if( this.hasClass(class_name) )
	  {
	      this.removeClass( class_name );
	  }
	  else
	  {
	      this.addClass( class_name );
	  }
       } 
    });
}


/**
 *  Put CKEditor iframe values in textareas
 *  
 *  @returns {undefined}
 */
function CKupdate()
{
    if(typeof CKEDITOR !== "undefined")
    {
	for ( var instance in CKEDITOR.instances )
	{
	    CKEDITOR.instances[instance].updateElement();
	}
    }
}


/**
 *  Toggle check of all checkboxes with elements_class name based by checked status of source element
 *  
 *  @param {Element} source Source element which triggers this event
 *  @param {string} targets_selector Target elements selector
 *  @return {bool} True if source element is checked
 */
function toggleCheck(source, targets_selector) 
{
    if( arguments.length < 2 )
    {
	console.log(ERROR_NOT_ENOUGH_ARGUMENTS);
    }
    
    var checkboxes = document.querySelectorAll( targets_selector );

    //For each set of checkboxes
    for(var i = 0; i < checkboxes.length; i++)
    {
	//Toggle check
	checkboxes[i].checked = source.checked;
    }
    
    return source.checked;
}


/**
 *  Check all checkboxes with elements_class name and source element
 *  
 *  @param {Element} source Source element which triggers this event
 *  @param {string} targets_selector Target elements selector
 *  @return {bool} True if source element is checked
 */
function check(source, targets_selector) 
{
    if( arguments.length < 2 )
    {
	console.log(ERROR_NOT_ENOUGH_ARGUMENTS);
    }
    
    var checkboxes = document.querySelectorAll( targets_selector );

    //For each set of checkboxes
    for(var i = 0; i < checkboxes.length; i++)
    {
	//Toggle check
	checkboxes[i].checked = true;
    }
    
    return document.querySelector(source).checked = true;
}