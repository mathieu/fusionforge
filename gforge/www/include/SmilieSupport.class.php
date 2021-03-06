<?php
require_once $gfcommon.'include/Error.class.php';

// smilies list
$SMILIES = array(
					':D' => array('image' => 'icon_biggrin.gif', 'emoticon' => 'Very happy'),
					':-D' => array('image' => 'icon_biggrin.gif', 'emoticon' => 'Very happy'),
					':grin:' => array('image' => 'icon_biggrin.gif', 'emoticon' => 'Very happy'),
					':)' => array('image' => 'icon_smile.gif', 'emoticon' => 'Smilie'),
					':-)' => array('image' => 'icon_smile.gif', 'emoticon' => 'Smilie'),
					':smile:' => array('image' => 'icon_smile.gif', 'emoticon' => 'Smilie'),
					':(' => array('image' => 'icon_sad.gif', 'emoticon' => 'Sad'),
					':-(' => array('image' => 'icon_sad.gif', 'emoticon' => 'Sad'),
					':sad:' => array('image' => 'icon_sad.gif', 'emoticon' => 'Sad'),
					':o' => array('image' => 'icon_surprised.gif', 'emoticon' => 'Surprised'),
					':-o' => array('image' => 'icon_surprised.gif', 'emoticon' => 'Surprised'),
					':eek:' => array('image' => 'icon_surprised.gif', 'emoticon' => 'Surprised'),
					':shock:' => array('image' => 'icon_eek.gif', 'emoticon' => 'Shocked'),
					':?' => array('image' => 'icon_confused.gif', 'emoticon' => 'Confused'),
					':-?' => array('image' => 'icon_confused.gif', 'emoticon' => 'Confused'),
					':???:' => array('image' => 'icon_confused.gif', 'emoticon' => 'Confused'),
					'8)' => array('image' => 'icon_cool.gif', 'emoticon' => 'Cool'),
					'8-)' => array('image' => 'icon_cool.gif', 'emoticon' => 'Cool'),
					':cool:' => array('image' => 'icon_cool.gif', 'emoticon' => 'Cool'),
					':lol:' => array('image' => 'icon_lol.gif', 'emoticon' => 'Laughing'),
					':x' => array('image' => 'icon_mad.gif', 'emoticon' => 'Mad'),
					':-x' => array('image' => 'icon_mad.gif', 'emoticon' => 'Mad'),
					':mad:' => array('image' => 'icon_mad.gif', 'emoticon' => 'Mad'),
					':P' => array('image' => 'icon_razz.gif', 'emoticon' => 'Razz'),
					':-P' => array('image' => 'icon_razz.gif', 'emoticon' => 'Razz'),
					':razz:' => array('image' => 'icon_razz.gif', 'emoticon' => 'Razz'),
					':oops:' => array('image' => 'icon_redface.gif', 'emoticon' => 'Embarassed'),
					':cry:' => array('image' => 'icon_cry.gif', 'emoticon' => 'Crying or Very sad'),
					':evil:' => array('image' => 'icon_evil.gif', 'emoticon' => 'Evil or Very Mad'),
					':twisted:' => array('image' => 'icon_twisted.gif', 'emoticon' => 'Twisted Evil'),
					':roll:' => array('image' => 'icon_rolleyes.gif', 'emoticon' => 'Rolling Eyes'),
					':wink:' => array('image' => 'icon_wink.gif', 'emoticon' => 'Wink'),
					';)' => array('image' => 'icon_wink.gif', 'emoticon' => 'Wink'),
					';-)' => array('image' => 'icon_wink.gif', 'emoticon' => 'Wink'),
					':!:' => array('image' => 'icon_exclaim.gif', 'emoticon' => 'Exclamation'),
					':?:' => array('image' => 'icon_question.gif', 'emoticon' => 'Question'),
					':idea:' => array('image' => 'icon_idea.gif', 'emoticon' => 'Idea'),
					':arrow:' => array('image' => 'icon_arrow.gif', 'emoticon' => 'Arrow'),
					':|' => array('image' => 'icon_neutral.gif', 'emoticon' => 'Neutral'),
					':-|' => array('image' => 'icon_neutral.gif', 'emoticon' => 'Neutral'),
					':neutral:' => array('image' => 'icon_neutral.gif', 'emoticon' => 'Neutral'),
					':mrgreen:' => array('image' => 'icon_mrgreen.gif', 'emoticon' => 'Mr. Green')
				);



class SmilieSupport extends Error {

	// 
	var $smilies_orig = array();
	var $smilies_repl = array();

	function SmilieSupport(){
		$this->Error();
		
		// we get all the smilies and we prepare them for display
		$this->initializeSmilies();
				
	}
	
	function prepareText($text){
		return $text;	
	}
	
	function displayText($text){
		if (count($this->smilies_orig)){ 	
		  $text = preg_replace($this->smilies_orig, $this->smilies_repl, ' ' . $text . ' '); 	
		  $text = substr($text, 1, -1); 
	   } 
	   return $text;
	}
	

	function initializeSmilies(){ 
		global $SMILIES, $sys_default_domain;
		
		$smilies_info = array();
		reset($SMILIES);
		
		while (list ($smilie_code, $smilie_info) = each ($SMILIES)) {
			$code = preg_quote($smilie_code);
			$code = str_replace('/', '\\/', $code);
			$this->smilies_orig[] = "/(?<=.\W|\W.|^\W)" . $code . "(?=.\W|\W.|\W$)/"; 
			$this->smilies_repl[] = '<img src="' . $sys_images_url . '/images/smiles/' . $smilie_info['image'] . '" alt="' . $smilie_info['emoticon'] . '" border="0" />';			
		}
	   return true;	
	}
	
	


}

?>
