import web
import numpy as np
import os
from PIL import Image
import chainer
from chainer import functions as F
from chainer import cuda, optimizers, serializers
import util

from neural_style import NeuralStyle, MRF
from net import VGG, VGG19
from lbfgs import LBFGS


class args:
	model = "vgg16.model"
	type = "vgg16"
	out_dir = "final/"
	gpu = 0
	iter = 150
	save_iter = 200
	lr = 1.0
	content_weight = 5
	style_weight = 100
	tv_weight = 1e-3
	width = 500
	content_layers = '3_3,4_3'
	style_layers = '1_2,2_2,3_3,4_3'
	initial_image = 'random'
	resolution_num = 1
	keep_color = False
	
vgg = VGG()
serializers.load_hdf5(args.model, vgg)
print 'loading neural network model completed'
optimizer = LBFGS(args.lr)

def open_and_resize_image(path, target_width, model):
    image = Image.open(path).convert('RGB')
    width, height = image.size
    target_height = int(round(float(height * target_width) / width))
    image = image.resize((target_width, target_height), Image.BILINEAR)
    return np.expand_dims(model.preprocess(np.asarray(image, dtype=np.float32), input_type='RGB'), 0)

def run(arg):
    content_image = open_and_resize_image('php/upload/'+arg.f+'.jpg', args.width, vgg)
    print 'loading content image completed'
    style_image = open_and_resize_image('php/'+arg.s, args.width, vgg)
    print 'loading style image completed'
    content_layers = args.content_layers.split(',')
    style_layers = args.style_layers.split(',')
    def on_epoch_done(epoch, x, losses):
    	print epoch
    model = NeuralStyle(vgg, optimizer, args.content_weight, args.style_weight, args.tv_weight, content_layers, style_layers, args.resolution_num, args.gpu, initial_image=args.initial_image, keep_color=args.keep_color)
    out_image = model.fit(content_image, style_image, args.iter, on_epoch_done)
    out_image = cuda.to_cpu(out_image.data)
    image = vgg.postprocess(out_image[0], output_type='RGB').clip(0, 255).astype(np.uint8)
    Image.fromarray(image).save('php/final/'+arg.f+'.png')


urls = (
    '/', 'index'
)

class index:
    def GET(self):
    	user_data = web.input()
    	print user_data
    	run(user_data)
        return "finished"

if __name__ == "__main__":
    app = web.application(urls, globals())
    app.run()