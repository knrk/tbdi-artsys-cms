/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/scripts
 */

/* --- class Art_Exception extends Error ------------------------------------ */
/**
 *  Basic exception to be used in whole system
 *  @extends {Error}
 *  @param {string} [message]
 *  @param {number} [number] (only for IE)
 *  @param {string} [description] (only for IE)
 *  @param {number} [stack_cut_count=1]
 *  @returns {Art_Exception}
 */
function Art_Exception( message, number, description, stack_cut_count )
{    
    stack_cut_count = undefVal(stack_cut_count, 1);
    
    this.message = message;
    this.name = this.constructor.name;
    this.stack = this.stackCutFirst((new Error).stack, stack_cut_count);
    
    //Firefox
    this.setFirefoxData();
 
    //IE
    this.description = description;
    this.number = number;
}
inherit(Art_Exception, Error);

Art_Exception.prototype.message = '';
Art_Exception.prototype.name = '';
Art_Exception.prototype.stack = '';
Art_Exception.prototype.columnNumber = 0;
Art_Exception.prototype.lineNumber = 0;
Art_Exception.prototype.fileName = '';
Art_Exception.prototype.description = '';
Art_Exception.prototype.number = 0;


/**
 *  Cut first lines from error stack
 *  
 *  @param {string} stack
 *  @param {number} [cut_count=1]
 *  @returns {string}
 */
Art_Exception.prototype.stackCutFirst = function( stack, cut_count )
{
    cut_count = undefVal(cut_count, 1);
    stack = undefVal(stack, '');
    
    return stack.split('\n').splice(cut_count).join('\n');
};


/**
 *  Set stack backtrace data for Firefox
 *  
 *  @returns {Art_Exception}
 */
Art_Exception.prototype.setFirefoxData = function()
{
    var split = this.stack.split('\n').splice(0,1).shift().split(':');
    this.columnNumber = split[split.length-1];
    this.lineNumber = split[split.length-2];
    this.fileName = split.splice(0,split.length-2).join(':').split('@')[1];
    
    return this;
};
/* -------------------------------------------------------------------------- */




/* --- class Art_InvalidArgumentsException extends Art_Exception ------------ */
/**
 *  Invalid arguments exception
 *  
 *  @constructor
 *  @extends {Art_Exception}
 *  @param {string} [message]
 *  @param {number} [number] (only for IE)
 *  @param {string} [description] (only for IE)
 *  @param {number} [stack_cut_count=2]
 *  @returns {Art_InvalidArgumentsException}
 */
function Art_InvalidArgumentsException( message, number, description, stack_cut_count )
{
    stack_cut_count = undefVal(stack_cut_count, 2);
    
    Art_Exception.call(this, message, number, description, stack_cut_count);
}
inherit(Art_InvalidArgumentsException, Art_Exception);
/* -------------------------------------------------------------------------- */




/* --- class Art_NotEnoughArgumentsException extends Art_Exception ---------- */
/**
 *  Not enough arguments exception
 *  
 *  @constructor
 *  @extends {Art_Exception}
 *  @param {string} [message]
 *  @param {number} [number] (only for IE)
 *  @param {string} [description] (only for IE)
 *  @param {number} [stack_cut_count=2]
 *  @returns {Art_NotEnoughArgumentsException}
 */
function Art_NotEnoughArgumentsException( message, number, description, stack_cut_count )
{
    stack_cut_count = undefVal(stack_cut_count, 2);
    
    Art_Exception.call(this, message, number, description, stack_cut_count);
}
inherit(Art_NotEnoughArgumentsException, Art_Exception);
/* -------------------------------------------------------------------------- */