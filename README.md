# Photobooth for Slovak National Gallery ArtData Hackathon


## Artistic style transfer web wrapper based on https://github.com/dsanno/chainer-neural-style


### PHP

main wrapper using FatFree Framework
Webcamera image grabbing using Webcam.JS

> cd php

> php -S localhost:1337

### Python

Follow https://github.com/dsanno/chainer-neural-style to install dependencies
You will need as well web.py
> pip install web.py
> python src/server.py 1338

### TODO

- nodejs wrapper for pinging the python part and managing the cue of images and responses to PHP
- form for getting e-mail address to send processed images to user
- web interface showing all processed images as tiles, with social interaction (ag like, share on faceboook, etc)
- correctly setup WebcamJS to crop images and diisplay correct sizes
- train style images to make use for Fast Style Transfer https://github.com/yusuketomoto/chainer-fast-neuralstyle