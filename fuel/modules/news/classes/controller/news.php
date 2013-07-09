<?php

namespace News;

class Controller_News extends \Controller_Base
{
	public function before()
	{
		parent::before();
		\Lang::load('news');
	}

	/**
	 * action_index
	 */
	public function action_index()
	{
		$config = array(
			'pagination_url' => 'news/index/',
			'total_items' => Model_News::query()->count(),
			'per_page'    => 10,
			'show_first'  => true,
			'show_last'   => true,
		);

		$pagination = \Pagination::forge('news', $config);

		$text = Model_News::query()
			->rows_offset($pagination->offset)
			->rows_limit($pagination->per_page)
			->order_by('created_at', 'desc')
			->get();

		$pagination = $pagination->render();

		$this->template->title = \Lang::get('index.title');
		$this->template->content = \View::forge('news::index', array(
			'text' => $text, 'pagination' => $pagination
		), false);
	}

	/**
	 * action_view
	 */
	public function action_view($id = null)
	{
		is_null($id) and \Response::redirect('news');

		//$news = Model_News::query()->where('id', '=', $id)->related('comments')->get_one();

		if ( ! $news = Model_News::find($id))
		{
			\Session::set_flash('error', \Lang::get('view.error', array('id' => $id)));
			\Response::redirect('news');
		}

		\Breadcrumb::set($news->title, null, 2);
		$this->template->title = $news->title;
		$this->template->content = \View::forge('news::view', array(
			'news' => $news,
		));
	}

	/**
	 * action_create
	 */
	public function action_create()
	{

		if ( ! \Sentry::check() || ! \Sentry::getUser()->hasAccess('admin'))
		{
			\Session::set_flash('error', \Lang::get('create.access'));
			\Response::redirect('news/index');
		}

		if (\Input::method() == 'POST')
		{
			$val = Model_News::validate('create');

			if ($val->run())
			{
				$post = Model_News::forge(array(
					'user_id' => $this->current_user->id,
					'title' => \Input::post('title'),
					'text' => \Input::post('text'),
				));

				if ($post and $post->save())
				{
					\Session::set_flash('success', \Lang::get('create.success'));
					\Response::redirect('news/index');
				}

				else
				{
					\Session::set_flash('error', \Lang::get('create.error'));
				}
			}
			else
			{
				\Session::set_flash('error', $val->error());
			}
		}

		$this->template->title = \Lang::get('create.title');
		$this->template->content = \View::forge('news::create');
	}
	public function action_createcomment($id = null)
	{
	    is_null($id) and \Response::redirect('news');
	    if (!\Sentry::check())
	    {
	    \Session::set_flash('error', \Lang::get('comments.access'));
	    \Response::redirect('news/view/'.$id);
	    }
	    if (\Input::method() == 'POST')
	    {
		$val = Model_Comment::validate('create');
		if ($val->run())
		{
		    $post = Model_Comment::forge(array(
			    'news_id' => $id,
			    'user_id' => $this->current_user->id,
			    'text' => \Input::post('text'),
			    ));
		    if ($post and $post->save())
		    {
		    \Session::set_flash('success', \Lang::get('comments.success'));
		    \Response::redirect('news/view/'.$id);
		    }
		    else
		    {
			\Session::set_flash('error', \Lang::get('comments.error'));
		    }
	    }
	    else
	    {
		\Session::set_flash('error', $val->error());
	    }
	}
	$this->template->title = \Lang::get('comments.title');
	$this->template->content = \View::forge('news::createcomment');
    }
}
