<h1 align="center">Welcome to container-pattern-php 👋</h1>
<p>
  <img alt="Version" src="https://img.shields.io/badge/version-1.0.0-blue.svg?cacheSeconds=2592000" />
  <a href="#" target="_blank">
    <img alt="License: MIT" src="https://img.shields.io/badge/License-MIT-yellow.svg" />
  </a>
</p>

> Container pattern for inversion of controle

### 🏠 [Homepage](https://github.com/niko-38500/container-pattern-php.git)

## Install

```sh
composer require life-style-coding/container-pattern
```

## Usage

step 1: To use it you have to provide the use statement for the package at your index.php 
```sh
use LifeStyleCoding\Container\Container;
```

step 2: Instanciate the container class and provide the controller and the methode you wish as arguments

step 3: run the resolve method of the container into a instance variable

step 4: run the execute method of the container and pass the instance variable as argument

exemple : 
```sh
$class = "\\App\\Controller\\HomeController";
$method = "index";
$container = new Container($class, $method);
$instance = $container->resolve();
$container->execute($instance);
```

## Next update

<p>improving injection to get class injected into class injected</p>
<img src="https://cdn-images-1.medium.com/max/1200/1*cwR_ezx0jliDvVUV6yno5g.jpeg">

## Author

👤 **Nicolas Montmayeur**

* Github: [@niko-38500](https://github.com/niko-38500)
* LinkedIn: [@nicolas-montmayeur-9b7b441ab](https://linkedin.com/in/nicolas-montmayeur-9b7b441ab)

## Show your support

Give a ⭐️ if this project helped you!

***
_This README was generated with ❤️ by [readme-md-generator](https://github.com/kefranabg/readme-md-generator)_