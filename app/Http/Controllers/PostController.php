<?php

namespace App\Http\Controllers;

//import model dan view
use App\Models\Post;
use Illuminate\View\View;

use Illuminate\Http\Request;

//tambahan
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    public function index() : View {

        //mengambil data post dari database
        $posts = Post::latest()->paginate(5);

        //menampilakn data ke view dengan membawa data "posts" yang sudah di ambil dari database
        return view("posts.index", compact("posts"));

    }

    //menampilkan halaman tambah data
    public function create(): View {
        return view("posts.create");
    }

    //menyimpan data ke database
    public function store(Request $request) {

        //menvalidasi data yang di inputkan user
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //menyimpan data post
        Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }





    public function destroy($id) {

        //mencari data data post dengan id yang di cari
        $post = Post::findOrFail($id);

        //menghapus file image/foto
        Storage::delete("public/posts/" .$post->image);

        //menghapus data post pada database
        $post->delete();

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    //menampilkan halaman edit data
    public function edit(string $id): View {

        $post = Post::findOrFail($id);
        return  view("posts.edit", compact("post"));
    }


    //memperbarui data di dabatase
    public function update(Request $request, $id) {

        //validasi
        $this->validate($request, [
            'image'     => 'image|mimes:jpeg,jpg,png|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        //get data from database
        $post = Post::findOrFail($id);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/'.$post->image);

            //update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        } else {

            //update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        }

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }



    

    //
    public function show(string $id) : View {
        //mengambil data dari database
        $post = Post::findOrFail($id);

        //menampilkan data ke view
        return view("posts.show", compact("post"));
    }

}
