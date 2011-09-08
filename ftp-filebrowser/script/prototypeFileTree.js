/* prototype File Tree Plugin
 *
 * Version 1.0
 *
 * Author:
 * -------
 *
 * Daniele Di Bernardo
 * marzapower.com (http://www.marzapower.com/)
 * 01 April 2008
 *
 *
 * Usage:
 * ------
 *
 *     $('myElementID').fileTree( options, callback )
 *
 * Options:  root             - root folder to display; default = './'
 *           script           - location of the serverside AJAX file to use; default = jqueryFileTree.php
 *           transitionStyle  - one of the default effect styles: 'appear', 'blind' or 'slide'; default = 'blind'
 *           transitionEffect - one of the transition effects from script.aculo.us or from a plugin; default = Effect.Transitions.sinoidal
 *           expandSpeed      - folder expanding speed in seconds; default = 0.3
 *           collapseSpeed    - folder collapsing speed in seconds; default = 0.3
 *           folderEvent      - event to trigger expand/collapse; default = 'click'
 *           loadMessage      - the message to display while initial tree loads (can be HTML)
 *
 * Requirements:
 * -------------
 *
 * This plugin has been tested with the latest available version of prototype (v. 1.6.0.2).
 * It will not work with prototype version < 1.6
 * This also needs script.aculo.us plugin for prototype to work correctly.
 * It has been tested to work with the latest available version of script.aculo.us (v. 1.8.1).
 *
 *
 * Terms of use:
 * -------------
 * 
 * prototype File Tree is licensed under a Creative Commons License and is copyrighted (C)2008 by Daniele Di Bernardo.
 * The license page can be found at: http://creativecommons.org/licenses/by-nc-sa/2.5/it/
 *
 * Credits:
 * --------
 *
 * This plugin takes inspiration from jQuery File Tree from Cory S.N. LaViska (A Beautiful Site - http://abeautifulsite.net/),
 * and is released under the same license (share-alike).
 *
 *
 * More info at http://www.marzapower.com/blog/show/211
 *
 */

Element.Methods.fileTree = function(element, o, callback) {
  
  // Defaults
  if (o.root == undefined)               o.root = './';
  if (o.script == undefined)             o.script = 'prototypeFileTree.php';
  if (o.transitionStyle == undefined)   o.transitionStyle = 'blind';
  if (o.folderEvent == undefined)       o.folderEvent = 'click';
  if (o.transitionEffect == undefined)  o.transitionEffect = Effect.Transitions.sinoidal;
  if (o.expandSpeed == undefined)       o.expandSpeed = 0.3;
  if (o.collapseSpeed == undefined)     o.collapseSpeed = 0.3;
  if (o.loadMessage == undefined)       o.loadMessage = 'Loading...';
  

    function showTree(c, t) {
      $(c).addClassName('wait');
      
      // Send Ajax request to populate the tree through a remote script
      new Ajax.Request(o.script, {
        parameters: { dir: t },
		method: 'post',
        onSuccess: function(data) {
          
          $(c).select('.start').invoke('remove');
          $(c).removeClassName('wait').insert(data.responseText);
          
          if (t == o.root) {
            $(c).select('div').invoke('show');
          }  else {
            myInnerDiv = $(c).select('div').first();
            // Transition effect when opening a folder
            new Effect.toggle(myInnerDiv, o.transitionStyle, {
              duration: o.expandSpeed,
              queue: 'end',
              transition: o.transitionEffect,
              afterFinish: function(obj) {
                $(c).removeClassName('collapsed').addClassName('expanded');
              }
            });
          }
          
          bindTree(c);
        }
      });
    }

    function bindTree(t) {
      $(t).select('li a').each( function(element) {
        element.observe(o.folderEvent, function(e) {
        
          myUl = $(this).up(1);
          myLi = $(this).up(0);
        
          if( myLi.hasClassName('directory') ) {
            
            // Folder elements
            if( myLi.hasClassName('collapsed') ) {              
              showTree( myLi, $(this).readAttribute('rel').match( /.*\// ) );
            } else {
              collapse(myLi);
            }
          } else {
            
            // Non folder elements
            callback($(this).readAttribute('rel'));
          }
          
          Event.stop(e);
        });
      });
        
      // Prevent anchors from triggering the # on non-click events
      if( o.folderEvent.toLowerCase != 'click' ) {
        $(t).select('li a').each( function(element) { 
          element.observe('click', function(e) {
            Event.stop(e);
          });
        });
      }
    }
    
    function collapse(myLi) {
      myInnerDiv = $(myLi).select('div').first();
      
      if (myInnerDiv == null) return;

      // Collapse
      new Effect.toggle(myInnerDiv, o.transitionStyle, {
        duration: o.collapseSpeed,
        queue: 'end',
        transition: o.transitionEffect,
        afterFinish: function(obj) {
          myInnerDiv.remove();
          myLi.removeClassName('expanded').addClassName('collapsed');
        }
      });
    }

    // Loading message
    $(element).innerHTML = '<ul class="prototypeFileTree start"><li class="wait">' + o.loadMessage + '</li></ul>';
    
    // Get the initial file list
    /*var intimeID = setTimeout(*/showTree( $(element), o.root );/*,'10000');*/
};

Element.addMethods();