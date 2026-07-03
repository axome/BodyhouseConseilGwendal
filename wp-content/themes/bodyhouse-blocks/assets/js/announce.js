(function () {
	var announce = document.querySelector('.bh-announce');
	var track = document.querySelector('.bh-announce__track');
	if (!announce || !track) return;

	var realSlides = Array.prototype.slice.call(track.children);
	if (realSlides.length < 2) return;

	var count = realSlides.length;
	var index = 0;
	var slideHeight = realSlides[0].offsetHeight;
	var intervalMs = 4000;
	var timer;
	var animating = false;

	function updateActive() {
		Array.prototype.forEach.call(track.children, function (slide, i) {
			slide.classList.toggle('is-active', i === index);
		});
	}

	function goTo(i, withTransition) {
		track.style.transition = withTransition === false ? 'none' : '';
		track.style.transform = 'translateY(-' + (i * slideHeight) + 'px)';
		updateActive();
	}

	// avance d'un cran vers le bas (message suivant, défile du haut vers le bas)
	function next() {
		if (animating) return;
		animating = true;
		index++;

		if (index === count) {
			// on continue visuellement sur un slide cloné du premier,
			// puis on revient instantanément au vrai premier slide
			track.appendChild(realSlides[0].cloneNode(true));
			goTo(index);
			track.addEventListener('transitionend', function reset() {
				track.removeEventListener('transitionend', reset);
				track.removeChild(track.lastElementChild);
				index = 0;
				goTo(index, false);
				animating = false;
			});
		} else {
			goTo(index);
			track.addEventListener('transitionend', function done() {
				track.removeEventListener('transitionend', done);
				animating = false;
			});
		}
	}

	// recule d'un cran (message précédent)
	function prev() {
		if (animating) return;
		animating = true;

		if (index === 0) {
			// on préfixe un clone du dernier slide, on saute dessus sans transition,
			// puis on anime vers lui
			var clone = realSlides[count - 1].cloneNode(true);
			track.insertBefore(clone, track.firstChild);
			index = 1;
			goTo(index, false);
			// force reflow avant d'activer la transition
			void track.offsetHeight;
			index = 0;
			goTo(index);
			track.addEventListener('transitionend', function reset() {
				track.removeEventListener('transitionend', reset);
				track.removeChild(track.firstElementChild);
				index = count - 1;
				goTo(index, false);
				animating = false;
			});
		} else {
			index--;
			goTo(index);
			track.addEventListener('transitionend', function done() {
				track.removeEventListener('transitionend', done);
				animating = false;
			});
		}
	}

	function start() {
		stop();
		timer = setInterval(next, intervalMs);
	}

	function stop() {
		clearInterval(timer);
	}

	var prevBtn = announce.querySelector('.bh-announce__nav--prev');
	var nextBtn = announce.querySelector('.bh-announce__nav--next');

	if (prevBtn) {
		prevBtn.addEventListener('click', function () {
			prev();
			start();
		});
	}
	if (nextBtn) {
		nextBtn.addEventListener('click', function () {
			next();
			start();
		});
	}

	window.addEventListener('resize', function () {
		slideHeight = realSlides[0].offsetHeight;
		goTo(index, false);
	});

	announce.addEventListener('mouseenter', stop);
	announce.addEventListener('mouseleave', start);

	realSlides[0].classList.add('is-active');
	goTo(0, false);
	start();
})();
