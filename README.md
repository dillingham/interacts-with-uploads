## InteractsWithUploads

Laravel trait adds FormRequest upload

<p>
    <a href="https://github.com/dillingham/interacts-with-uploads/actions">
        <img src="https://github.com/dillingham/interacts-with-uploads/workflows/tests/badge.svg" alt="Build Status">
    </a>
    <a href="https://packagist.org/packages/dillingham/interacts-with-uploads">
        <img src="https://img.shields.io/packagist/v/dillingham/interacts-with-uploads" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/dillingham/interacts-with-uploads">
        <img src="https://img.shields.io/packagist/dt/dillingham/interacts-with-uploads" alt="Total Downloads">
    </a>
    <a href="https://twitter.com/im_brian_d">
        <img src="https://img.shields.io/twitter/follow/im_brian_d?color=%231da1f1&label=Twitter&logo=%231da1f1&logoColor=%231da1f1&style=flat-square" alt="twitter">
    </a>
</p>


## Install
```
composer require dillingham/interacts-with-uploads
```

And add the following trait to your FormRequest classes

```php
use \Dillingham\InteractsWithUploads;
``` 

Now you can call the following methods in controllers:

---

### New uploads 

```
$request->upload('key')
```

```php
public function store(CreateProfileRequest $request) 
{
    $request->upload('banner');

    $profile = Profile::create($request->validated());

    return redirect()->route('profiles.show', $profile);
}
```
Store the file and add it's path to `validated()` for the same key: `banner`

This makes `$profile->banner` equal to `/uploads/asfos8asfoafaf9q3wf.jpg`

---

### Update uploads

```
$request->upload('binding.key')
```
 
```php
public function update(UpdateProfileRequest $request, Profile $profile)
{
    $request->upload('profile.banner');

    $profile->update($request->validated());

    return redirect()->route('profiles.show', $profile);
}
```
If the request has a new file for `key`, it deletes the old and uploads the new

If no new file is in the request, it will add the original file path back to `validated()`

So submitting `null` for that `key` will result in the same path / no changes to the model.

It will get the original file path using the `key` & [route model binding](https://laravel.com/docs/routing#route-model-binding), in this scenario.. `profile`

The binding `profile` connects a typehinted model & defined in `Route::`  using `{binding}` syntax

---

### Manual update 

If you are not using route model binding, you can manually set the path to update.

```php
public function update(UpdateProfileRequest $request, Profile $profile)
{
    $request->updateUpload($profile, 'banner');

    $profile->update($request->validated());

    return redirect()->route('profiles.show', $profile);
}
```

## Parameters

You can specify a few options and override the defaults.
```php
$request->upload('banner', 'uploads', 'public');
```
- `uploads`: is the default path within storage 
- `public`: is the default disk within config/filesystems

### Author

[@im_brian_d](https://twitter.com/im_brian_d) 
