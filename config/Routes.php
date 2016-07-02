<?php
/**
 * Configuration of the routes
 */

final class Routes extends RoutesAbstract {
	
	/**
	 * List of the routes
	 *
	 *	{
	 *		"module" : {	// Name of the route. e.g. : 'user_view'
	 *			regexp : regular expression (PCRE) matching with the url. e.g. : '^user/([a-z0-9]+)/(?=\?|$)'
	 *			vars : variables corresponding to the url, with values of the previous regexp. e.g. : 'controller=User&action=view&username=$1'
	 *			url : URL of the page depending on various parameters ({id}, {title}...). e.g. : 'users/{username}/'
	 *			extend (optionnal) : {
	 *				"vars1" : "module_extended1"
	 *				"vars2" : "module_extended2"
	 *				...
	 *				// vars1 = names of additional variables (seperated by &). e.g. : 'page'
	 *				// module_extended1 = route name to use when these variables are defined : 'user_view_page'
	 *			}
	 *		},
	 *	...}
	 *
	 * @static array
	 */
	protected static $routes =	array(
		// Home
		'posts'	=> array(
			'regexp'	=> '^(?=\?|$)',
			'vars'		=> 'controller=Post&action=index',
			'url'		=> ''
		),
		'posts_ajax_page'	=> array(
			'regexp'	=> '^ajax/posts/([01])/([1-9][0-9]*)(?=\?|$)',
			'vars'		=> 'controller=Post&action=index_ajax&official=$1&page=$2&mode=raw',
			'url'		=> 'ajax/posts/{official}/{page}'
		),
		
		// Posts by category
		'posts_category'	=> array(
			'regexp'	=> '^category/([a-zA-Z0-9_-]+)(?=\?|$)',
			'vars'		=> 'controller=Post&action=index&category=$1',
			'url'		=> 'category/{category}'
		),
		'posts_category_ajax_page'	=> array(
			'regexp'	=> '^ajax/category/([a-zA-Z0-9_-]+)/([01])/([1-9][0-9]*)(?=\?|$)',
			'vars'		=> 'controller=Post&action=index_ajax&category=$1&official=$2&page=$3&mode=raw',
			'url'		=> 'ajax/category/{category}/{official}/{page}'
		),
		
		
		// Post
		'post'	=> array(
			'regexp'	=> '^post/([0-9]+)(?=\?|$)',
			'vars'		=> 'controller=Post&action=view&id=$1',
			'url'		=> 'post/{id}'
		),
		
		// Add a post
		'post_add'	=> array(
			'regexp'	=> '^post/add(?=\?|$)',
			'vars'		=> 'controller=Post&action=iframe_add&mode=iframe',
			'url'		=> 'post/add'
		),
		'attachment_add'	=> array(
			'regexp'	=> '^post/add/([0-9]+)(?=\?|$)',
			'vars'		=> 'controller=Post&action=addAttachment&id=$1&mode=iframe',
			'url'		=> 'post/add/{id}'
		),
		
		// Delete post
		'post_delete'	=> array(
			'regexp'	=> '^ajax/post/([0-9]+)/delete(?=\?|$)',
			'vars'		=> 'controller=Post&action=delete&id=$1&mode=json',
			'url'		=> 'ajax/post/{id}/delete'
		),
		
		// Delete attachment
		'attachment_delete'	=> array(
			'regexp'	=> '^ajax/post/([0-9]+)/([0-9]+)/delete(?=\?|$)',
			'vars'		=> 'controller=Post&action=deleteattachment&post_id=$1&id=$2&mode=json',
			'url'		=> 'ajax/post/{post_id}/{id}/delete'
		),
		
		// Add a comment to a post
		'post_comment'	=> array(
			'regexp'	=> '^ajax/post/([0-9]+)/comment/add(?=\?|$)',
			'vars'		=> 'controller=PostComment&action=add&post_id=$1&mode=raw',
			'url'		=> 'ajax/post/{id}/comment/add'
		),
		
		// Delete comment post
		'post_comment_delete'	=> array(
			'regexp'	=> '^ajax/post/comment/([0-9]+)/delete(?=\?|$)',
			'vars'		=> 'controller=PostComment&action=delete&id=$1&mode=json',
			'url'		=> 'ajax/post/comment/{id}/delete'
		),
			
		// Add a Like
		'like'	=> array(
			'regexp'	=> '^ajax/like/([0-9]+)/add(?=\?|$)',
			'vars'		=> 'controller=PostLike&action=add&post_id=$1&mode=raw',
			'url'		=> 'ajax/like/{post_id}/add'
		),
		
		// Delete Like
		'like_delete'	=> array(
			'regexp'	=> '^ajax/like/([0-9]+)/delete(?=\?|$)',
			'vars'		=> 'controller=PostLike&action=delete&post_id=$1&mode=json',
			'url'		=> 'ajax/likecom/{id}/delete'
		),
            
                // Add a comment Like
		'like_comment'	=> array(
			'regexp'	=> '^ajax/likecom/([0-9]+)/add(?=\?|$)',
			'vars'		=> 'controller=PostCommentLike&action=add&post_id=$1&mode=raw',
			'url'		=> 'ajax/likecom/{post_id}/add'
		),
		
		// Delete comment Like
		'like_comment_delete'	=> array(
			'regexp'	=> '^ajax/likecom/([0-9]+)/delete(?=\?|$)',
			'vars'		=> 'controller=PostCommentLike&action=delete&post_id=$1&mode=json',
			'url'		=> 'ajax/likecom/{id}/delete'
		),
				// Add a disLike
		'dislike'	=> array(
			'regexp'	=> '^ajax/dislike/([0-9]+)/add(?=\?|$)',
			'vars'		=> 'controller=PostDislike&action=add&post_id=$1&mode=raw',
			'url'		=> 'ajax/dislike/{post_id}/add'
		),
		
		// Delete Like
		'dislike_delete'	=> array(
			'regexp'	=> '^ajax/dislike/([0-9]+)/delete(?=\?|$)',
			'vars'		=> 'controller=PostDislike&action=delete&post_id=$1&mode=json',
			'url'		=> 'ajax/dislikecom/{id}/delete'
		),
            
                // Add a comment Like
		'dislike_comment'	=> array(
			'regexp'	=> '^ajax/dislikecom/([0-9]+)/add(?=\?|$)',
			'vars'		=> 'controller=PostCommentDislike&action=add&post_id=$1&mode=raw',
			'url'		=> 'ajax/dislikecom/{post_id}/add'
		),
		
		// Delete comment Like
		'dislike_comment_delete'	=> array(
			'regexp'	=> '^ajax/dislikecom/([0-9]+)/delete(?=\?|$)',
			'vars'		=> 'controller=PostCommentDislike&action=delete&post_id=$1&mode=json',
			'url'		=> 'ajax/dislikecom/{id}/delete'
		),
		// Events' posts in a month
		'events'	=> array(
			'regexp'	=> '^events/([0-9]{4})/([0-9]{2})(?=\?|$)',
			'vars'		=> 'controller=Post&action=events&year=$1&month=$2',
			'url'		=> 'events/{year}/{month}',
			'extend'	=> array(
				'day&group'	=> 'group_events_day',
				'day'	=> 'events_day',
				'group'	=> 'group_events'
			)
		),
		'group_events'	=> array(
			'regexp'	=> '^association/([a-z0-9-]+)/events/([0-9]{4})/([0-9]{2})(?=\?|$)',
			'vars'		=> 'controller=Post&action=events&group=$1&year=$2&month=$3',
			'url'		=> 'association/{group}/events/{year}/{month}',
			'extend'	=> array(
				'day'	=> 'events_group_day'
			)
		),
		// Events' posts in a day
		'events_day'	=> array(
			'regexp'	=> '^events/([0-9]{4})/([0-9]{2})/([0-9]{2})(?=\?|$)',
			'vars'		=> 'controller=Post&action=events&year=$1&month=$2&day=$3',
			'url'		=> 'events/{year}/{month}/{day}',
			'extend'	=> array(
				'group'	=> 'group_events_day'
			)
		),
		'group_events_day'	=> array(
			'regexp'	=> '^association/([a-z0-9-]+)/events/([0-9]{4})/([0-9]{2})/([0-9]{2})(?=\?|$)',
			'vars'		=> 'controller=Post&action=events&group=$1&year=$2&month=$3&day=$4',
			'url'		=> 'association/{group}/events/{year}/{month}/{day}'
		),
		
		// iCal : Official events
		'ical_official'	=> array(
			'regexp'	=> '^events/calendar-official.ics(?=\?|$)',
			'vars'		=> 'controller=Event&action=ical&official&mode=raw',
			'url'		=> 'events/calendar-official.ics',
			'extend'	=> array(
				'group'	=> 'group_ical_official'
			)
		),
		'group_ical_official'	=> array(
			'regexp'	=> '^association/([a-z0-9-]+)/events/calendar-official.ics(?=\?|$)',
			'vars'		=> 'controller=Event&action=ical&official&group=$1&mode=raw',
			'url'		=> 'association/{group}/events/calendar-official.ics'
		),
		// iCal : Non official events
		'ical_non_official'	=> array(
			'regexp'	=> '^events/calendar-students.ics(?=\?|$)',
			'vars'		=> 'controller=Event&action=ical&mode=raw',
			'url'		=> 'events/calendar-students.ics',
			'extend'	=> array(
				'group'	=> 'group_ical_non_official'
			)
		),
		'group_ical_non_official'	=> array(
			'regexp'	=> '^association/([a-z0-9-]+)/events/calendar-students.ics(?=\?|$)',
			'vars'		=> 'controller=Event&action=ical&group=$1&mode=raw',
			'url'		=> 'association/{group}/events/calendar-students.ics'
		),
		
		// Vote for a survey
		'survey_vote'	=> array(
			'regexp'	=> '^ajax/survey/vote/([0-9]+)(?=\?|$)',
			'vars'		=> 'controller=Survey&action=vote&id=$1&mode=raw',
			'url'		=> 'ajax/survey/vote/{id}'
		),
		
		// Student profile
		'student'	=> array(
			'regexp'	=> '^student/([a-z0-9-]+)(?=\?|$)',
			'vars'		=> 'controller=Student&action=view&username=$1',
			'url'		=> 'student/{username}'
		),
		'user_posts_ajax_page'	=> array(
			'regexp'	=> '^ajax/user/([0-9]+)/posts/([1-9][0-9]*)(?=\?|$)',
			'vars'		=> 'controller=Post&action=index_ajax&user_id=$1&official=0&page=$2&mode=raw',
			'url'		=> 'ajax/user/{user_id}/posts/{page}'
		),
		'user_posts_category'	=> array(
			'regexp'	=> '^student/([a-z0-9-]+)/category/([a-zA-Z0-9_-]+)(?=\?|$)',
			'vars'		=> 'controller=Student&action=view&username=$1&category=$2',
			'url'		=> 'student/{username}/category/{category}'
		),
		'user_posts_category_ajax_page'	=> array(
			'regexp'	=> '^ajax/user/([0-9]+)/category/([a-zA-Z0-9_-]+)/([1-9][0-9]*)(?=\?|$)',
			'vars'		=> 'controller=Post&action=index_ajax&user_id=$1&official=0&category=$2&page=$3&mode=raw',
			'url'		=> 'ajax/user/{user_id}/category/{category}/{page}'
		),
		
		// Edit a user
		'student_edit'	=> array(
			'regexp'	=> '^student/([a-z0-9-]+)/edit(?=\?|$)',
			'vars'		=> 'controller=Student&action=edit&username=$1',
			'url'		=> 'student/{username}/edit'
		),
		
		// Edit personnal information
		'profile_edit'	=> array(
			'regexp'	=> '^profile/edit(?=\?|$)',
			'vars'		=> 'controller=User&action=profile_edit',
			'url'		=> 'profile/edit'
		),
		
		// Students' directory
		'students'	=> array(
			'regexp'	=> '^students(?=\?|$)',
			'vars'		=> 'controller=Student&action=index',
			'url'		=> 'students'
		),
		'students_promo' =>array(
			'regexp'	=> '^ajax/students_promo/([0-9]+)(?=\?|$)',
			'vars'		=> 'controller=Student&action=oldPromo&index=$1&mode=raw',
			'url'		=> 'ajax/students_promo'
		),
		
		// Group's page
		'group'	=> array(
			'regexp'	=> '^association/([a-z0-9-]+)(?=\?|$)',
			'vars'		=> 'controller=Group&action=view&group=$1',
			'url'		=> 'association/{group}'
		),
		'group_posts_ajax_page'	=> array(
			'regexp'	=> '^ajax/association/([a-z0-9-]+)/posts/([1-9][0-9]*)(?=\?|$)',
			'vars'		=> 'controller=Post&action=index_ajax&group=$1&page=$2&mode=raw',
			'url'		=> 'ajax/association/{group}/posts/{page}'
		),
		'group_posts_category'	=> array(
			'regexp'	=> '^association/([a-z0-9-]+)/category/([a-zA-Z0-9_-]+)(?=\?|$)',
			'vars'		=> 'controller=Group&action=view&group=$1&category=$2',
			'url'		=> 'association/{group}/category/{category}'
		),
		'group_posts_category_ajax_page'	=> array(
			'regexp'	=> '^ajax/association/([a-z0-9-]+)/category/([a-zA-Z0-9_-]+)/([1-9][0-9]*)(?=\?|$)',
			'vars'		=> 'controller=Post&action=index_ajax&group=$1&category=$2&page=$3&mode=raw',
			'url'		=> 'ajax/association/{group}/category/{category}/{page}'
		),
		'group_edit'	=> array(
			'regexp'	=> '^association/([a-z0-9-]+)/edit(?=\?|$)',
			'vars'		=> 'controller=Group&action=edit&group=$1',
			'url'		=> 'association/{group}/edit'
		),
		'group_delete'	=> array(
			'regexp'	=> '^association/([a-z0-9-]+)/delete(?=\?|$)',
			'vars'		=> 'controller=Group&action=delete&group=$1',
			'url'		=> 'association/{group}/delete'
		),
		'group_add'	=> array(
			'regexp'	=> '^association-add(?=\?|$)',
			'vars'		=> 'controller=Group&action=add',
			'url'		=> 'association-add'
		),
		
		
		// Associations' list
		'groups'	=> array(
			'regexp'	=> '^associations(?=\?|$)',
			'vars'		=> 'controller=Group&action=index',
			'url'		=> 'associations'
		),
		
		// Sign-in
		'signin'	=> array(
			'regexp'	=> '^signin$',
			'vars'		=> 'controller=User&action=signin&redirect=/',
			'url'		=> 'signin',
			'extend'	=> array(
				'redirect'	=> 'signin_redirect'
			)
		),
		'signin_redirect'	=> array(
			'regexp'	=> '^signin(/.*)$',
			'vars'		=> 'controller=User&action=signin&redirect=$1',
			'url'		=> 'signin{redirect}'
		),
		// Logout
		'logout'	=> array(
			'regexp'	=> '^logout$',
			'vars'		=> 'controller=User&action=logout&redirect=/',
			'url'		=> 'logout',
			'extend'	=> array(
				'redirect'	=> 'logout_redirect'
			)
		),
		'logout_redirect'	=> array(
			'regexp'	=> '^logout(/.*)$',
			'vars'		=> 'controller=User&action=logout&redirect=$1',
			'url'		=> 'logout{redirect}'
		),

        'api_login' => array(
            'regexp'    => '^api/login(?=\?|$)',
            'vars'		=> 'controller=Api&action=login&mode=json',
            'url'       => 'api/login'
        ),

        'api_posts'	=> array(
            'regexp'	=> '^api/posts(?=\?|$)',
            'vars'		=> 'controller=Post&action=lastsPostsApi&mode=json',
            'url'		=> 'api/posts'
        ),

        'api_post'	=> array(
            'regexp'	=> '^api/post/([0-9]+)(?=\?|$)',
            'vars'		=> 'controller=Post&action=viewApi&id=$1&mode=json',
            'url'		=> 'api/post/{id}'
        ),

        'api_media'	=> array(
            'regexp'	=> '^api/media(?=\?|$)',
            'vars'		=> 'controller=Media&action=api&mode=json',
            'url'		=> 'api/media'
        ),

        'api_comment'	=> array(
            'regexp'	=> '^api/postcomment(?=\?|$)',
            'vars'		=> 'controller=PostComment&action=addApi&mode=json',
            'url'		=> 'api/postcomment'
        ),

        'api_like'	=> array(
            'regexp'	=> '^api/postlike(?=\?|$)',
            'vars'		=> 'controller=PostLike&action=addApi&mode=json',
            'url'		=> 'api/postlike'
        ),

        'api_dislike'	=> array(
            'regexp'	=> '^api/postdislike(?=\?|$)',
            'vars'		=> 'controller=PostDislike&action=addApi&mode=json',
            'url'		=> 'api/postdislike'
        ),

        'api_student'	=> array(
            'regexp'	=> '^api/student/([a-z0-9-]+)(?=\?|$)',
            'vars'		=> 'controller=Student&action=viewApi&username=$1&mode=json',
            'url'		=> 'api/student/{username}'
        ),

        'api_students'	=> array(
            'regexp'	=> '^api/students(?=\?|$)',
            'vars'		=> 'controller=Student&action=listApi&mode=json',
            'url'		=> 'api/students'
        ),

        'api_group'	=> array(
            'regexp'	=> '^api/association/([a-z0-9-]+)(?=\?|$)',
            'vars'		=> 'controller=Group&action=viewApi&group=$1&mode=json',
            'url'		=> 'api/association/{group}'
        ),

        'api_groups'	=> array(
            'regexp'	=> '^api/associations(?=\?|$)',
            'vars'		=> 'controller=Group&action=listApi&mode=json',
            'url'		=> 'api/associations'
        ),

        'api_register'	=> array(
            'regexp'	=> '^api/register(?=\?|$)',
            'vars'		=> 'controller=Api&action=register&mode=json',
            'url'		=> 'api/register'
        ),

        /*'test_gcm'      => array(
            'regexp'	=> '^test/gcm(?=\?|$)',
            'vars'		=> 'controller=Api&action=testGCM',
            'url'		=> 'test/gcm'
        ),*/

		// Search and Auto completion
		'search'	=> array(
			'regexp'	=> '^search(?=\?|$)',
			'vars'		=> 'controller=Search&action=index',
			'url'		=> 'search'
		),
		'autocomplete'	=> array(
			'regexp'	=> '^ajax/autocomplete(?=\?|$)',
			'vars'		=> 'controller=Search&action=autocomplete&mode=json',
			'url'		=> 'ajax/autocomplete'
		),
		'autocompletion_student_name'	=> array(
			'regexp'	=> '^ajax/autocomplete/student/name(?=\?|$)',
			'vars'		=> 'controller=Student&action=autocomplete&mode=json',
			'url'		=> 'ajax/autocomplete/student/name'
		),

        // Extra : Isep D'OR
        'isep_or_1'	=> array(
			'regexp'	=> '^isep-d-or$',
			'vars'		=> 'controller=IsepOr&action=firstRound',
			'url'		=> 'isep-d-or'
		),
        'isep_or_2'	=> array(
			'regexp'	=> '^isep-d-or/final$',
			'vars'		=> 'controller=IsepOr&action=finalRound',
			'url'		=> 'isep-d-or/final'
		),
        'isep_or_3'	=> array(
			'regexp'	=> '^isep-d-or/result$',
			'vars'		=> 'controller=IsepOr&action=result',
			'url'		=> 'isep-d-or/result'
		),
        'autocomplete_isepor'	=> array(
			'regexp'	=> '^ajax/isepor/autocomplete(?=\?|$)',
			'vars'		=> 'controller=IsepOr&action=IsepOrAutocomplete&mode=json',
			'url'		=> 'ajax/isepor/autocomplete'
		),
		//Media's Liste
		'media'	=> array(
			'regexp'	=> '^media(?=\?|$)',
			'vars'		=> 'controller=Media&action=index',
			'url'		=> 'media'
		),
		//Administration
		'admin'	=> array(
			'regexp'	=> '^administration/([a-z]+)(?=\?|$)',
			'vars'		=> 'controller=Administration&action=index&nav=$1',
			'url'		=> 'administration/{nav}'
		),
		'adminexport'	=> array(
			'regexp'	=> '^adminexport/([0-9]+)(?=\?|$)',
			'vars'		=> 'controller=Administration&action=exportDB&type=$1',
			'url'		=> 'adminexport/{type}'
		),
		'admindelete'	=> array(
			'regexp'	=> '^admindelete/([0-9]+)/([0-9]+)(?=\?|$)',
			'vars'		=> 'controller=Administration&action=delete&type=$1&id=$2',
			'url'		=> 'admindelete/{type}/{id}'
		),
		'admindelete2'	=> array(
			'regexp'	=> '^admindelete2/([a-z0-9- -]+)(?=\?|$)',
			'vars'		=> 'controller=Administration&action=deleteadmin&username=$1',
			'url'		=> 'admindelete2/{username}'
		),
		
	);

}
