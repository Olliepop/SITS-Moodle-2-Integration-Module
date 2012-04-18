<?php
/*
 * @package    enrol
 * @subpackage sits
 * @copyright  2011 University of Bath
 * @author     Alex Lydiate {@link http://alexlydiate.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once('edit_form.php');

$courseid = required_param('courseid', PARAM_INT);

$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
$context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

require_login($course);
$plugin = enrol_get_plugin('sits');

if ($instances = $DB->get_records('enrol', array('courseid'=>$course->id, 'enrol'=>'sits'), 'id ASC')) {
	$instance = array_shift($instances);
	if ($instances) {
		// we allow only one instance per course!!
		foreach ($instances as $del) {
			$plugin->delete_instance($del);
		}
	}
} else {
	require_capability('moodle/course:enrolconfig', $context);
	// no instance yet, we can add new instance
	navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id'=>$course->id)));
	$instance = new stdClass();
	$instance->id       = null;
	$instance->courseid = $course->id;
	$fields = array('status'=>0, 'enrolperiod'=>$data->enrolperiod, 'roleid'=>$data->roleid);
    $plugin->add_instance($course, $fields);
}

$return = new moodle_url('/enrol/instances.php', array('id'=>$course->id));
redirect($return);
