const projectImages = document.querySelectorAll('.projects .project__image');

// Function to calculate the parallax offset for desktop (0 to -maxOffsetVh)
function calculateParallaxOffsetDesktop(progress, isFirstElement = false) {
    const screenHeight = window.innerHeight;
    const maxOffsetVh = 20; // Maximum offset in vh

    let yOffsetVh;
    if (isFirstElement) {
        yOffsetVh = -progress * maxOffsetVh; // 0 to -maxOffsetVh
    } else {
        yOffsetVh = -progress * maxOffsetVh; // Also 0 to -maxOffsetVh
    }

    const yOffsetPx = (yOffsetVh / 100) * screenHeight;
    return yOffsetPx;
}

// Function to calculate the parallax offset for mobile (0 to +maxOffsetVh)
function calculateParallaxOffsetMobile(progress) {
    const screenHeight = window.innerHeight;
    const maxOffsetVh = 20; // Maximum offset in vh

    // 0 to +maxOffsetVh
    const yOffsetVh = progress * maxOffsetVh;
    const yOffsetPx = (yOffsetVh / 100) * screenHeight;
    return yOffsetPx;
}

function applyParallaxMobile() {
    projectImages.forEach((image) => {
        const video = image.querySelector('video');

        ScrollTrigger.create({
            trigger: image,
            start: "top top",
            end: "bottom top",
            scrub: true,
            onUpdate: (self) => {
                const yOffset = calculateParallaxOffsetMobile(self.progress);
                if (video) {
                    video.style.transform = `translateY(${yOffset}px)`;
                } else {
                    image.style.backgroundPositionY = `${yOffset}px`;
                }
            },
            // Autoplay on scroll ONLY if autoplay is 'scroll'
            onEnter: () => { if (video && image.dataset.autoplay === 'scroll') video.play(); },
            onLeaveBack: () => { if (video && image.dataset.autoplay === 'scroll') video.pause(); },
        });
    });
}

function applyPinningAndParallaxDesktop() {
    // Handle the first element (pinning and parallax)
    if (projectImages.length > 0) {
        const firstImage = projectImages[0];
        const firstVideo = firstImage.querySelector('video');

        ScrollTrigger.create({
            trigger: firstImage,
            pin: firstImage.querySelector('.project__image-pin'), // Pin the inner content
            start: "top top",
            end: "bottom top",
            scrub: true,
             // Autoplay on scroll ONLY if autoplay is 'scroll'
            onEnter: () => { if (firstVideo && firstImage.dataset.autoplay === 'scroll') firstVideo.play(); },
            onLeaveBack: () => { if (firstVideo && firstImage.dataset.autoplay === 'scroll') firstVideo.pause(); },
        });

        ScrollTrigger.create({
            trigger: firstImage,
            start: "top top",
            end: "bottom top",
            scrub: true,
            onUpdate: (self) => {
                const yOffset = calculateParallaxOffsetDesktop(self.progress, true);
                if (firstVideo) {
                    firstVideo.style.transform = `translateY(${yOffset}px)`;
                } else {
                    firstImage.style.backgroundPositionY = `${yOffset}px`;
                }
            }
        });
    }

    // Handle the middle elements (pinning and parallax)
    projectImages.forEach((image, index) => {
        if (index < projectImages.length - 1) {
            const nextImage = projectImages[index + 1];
            const nextVideo = nextImage.querySelector('video');

            ScrollTrigger.create({
                trigger: image,
                pin: nextImage.querySelector('.project__image-pin'), // Pin the *next* image's inner content
                start: "top top",
                end: "bottom top-=100%",  // Adjust end for overlap
                scrub: true,
                // Autoplay on scroll ONLY if autoplay is 'scroll'
                onEnter: () => { if (nextVideo && nextImage.dataset.autoplay === 'scroll') nextVideo.play(); },
                onLeaveBack: () => { if (nextVideo && nextImage.dataset.autoplay === 'scroll') nextVideo.pause(); },
            });

            ScrollTrigger.create({
                trigger: image,
                start: "top top",
                end: "bottom top",
                scrub: true,
                onUpdate: (self) => {
                    const yOffset = calculateParallaxOffsetDesktop(self.progress);
                    if (nextVideo) {
                        nextVideo.style.transform = `translateY(${yOffset}px)`;
                    } else {
                        nextImage.style.backgroundPositionY = `${yOffset}px`; // Apply to the NEXT image
                    }
                }
            });
        }
    });

    // Handle the last element (pinning, NO parallax)
    if (projectImages.length > 1) {
      const lastImage = projectImages[projectImages.length - 1];
      const lastVideo = lastImage.querySelector('video');
        ScrollTrigger.create({
          trigger: projectImages[projectImages.length - 2],
          pin: lastImage.querySelector('.project__image-pin'),
          start: 'top top',
          end: 'bottom top',
          scrub: true,
           // Autoplay on scroll ONLY if autoplay is 'scroll'
          onEnter: () => { if (lastVideo && projectImages[projectImages.length - 2].dataset.autoplay === 'scroll') lastVideo.play(); },
          onLeaveBack: () => { if (lastVideo &&  projectImages[projectImages.length - 2].dataset.autoplay === 'scroll') lastVideo.pause(); },
        })
    }
}

// Check screen width and apply appropriate effects
function checkScreenWidth() {
    const screenWidth = window.innerWidth;
    ScrollTrigger.getAll().forEach(trigger => trigger.kill()); // Kill all existing ScrollTriggers

    // Autoplay on LOAD (this is the ONLY place we use data-autoplay for playing)
    projectImages.forEach((image) => {
        const video = image.querySelector('video');
        if (video && image.dataset.autoplay === 'load') {
            video.play();
        }
    });

    if (screenWidth < 768) {
        applyParallaxMobile();
    } else {
        applyPinningAndParallaxDesktop();
    }
}

// Initial check
checkScreenWidth();

// Check on resize
window.addEventListener('resize', checkScreenWidth);