<?
####################################
#
# Member Functions
#
####################################


# getAge
####################################
function getAge($member, $precise = false) {
	
	$use = is_object($member) ? $member->birthdate : $member;

	$age = (time() - strtotime($use)) /60/60/24/365;
	
	if ($precise != false)
		return age;
	else
		return floor($age);
}


# getFullName
####################################
function getFullName($member) {
	
	return $member->firstname . " " . $member->lastname;

}
