/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/scripts
 *  
 *  This file is included as first
 */

window.$hb = $('html, body');
window.ERROR_NOT_ENOUGH_ARGUMENTS = 'Not enough arguments supploed';
window.ERROR_INVALID_ARGUMENTS = 'Invalid arguments supplied';
window.ERROR_JQUERY_NOT_LOADED = 'jQuery not loaded';



/* --- class Art_Component -------------------------------------------------- */
function Art_Component() 
{
    //Save component for later use
    Art_Component.components.push(this);
};

/**
 *  @type Art_Component[]
 */
Art_Component.components = [];
Art_Component.prototype.initialized = false;


/**
 * Initialize component
 * 
 * @returns {Art_Component}
 */
Art_Component.prototype.initialize = function() 
{
    if( !this.initialized )
    {
	//Do init
    }
    
    return this;
};

/**
 * Initialize all components
 * 
 * @returns {undefined}
 */
Art_Component.initializeAll = function() 
{
    for( var i = 0; i < Art_Component.components.length; i++ )
    {
	Art_Component.components[i].initialize();
    }
};
//-----------------------------------------------------------------------------/