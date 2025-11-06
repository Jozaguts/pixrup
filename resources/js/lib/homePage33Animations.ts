import { gsap } from './gsap';
import { ensureSpringer } from './vendor/springer';
import { ensureStackCards, requestStackCardsUpdate } from './vendor/stackCards';

const SELECTOR = '[data-ns-animate]';

const initRevealElements = () => {
  const elements = document.querySelectorAll<HTMLElement>(SELECTOR);
  if (!elements.length) {
    return;
  }

  const springFactory = window.Springer?.default ?? null;

  elements.forEach((elem) => {
    const duration = elem.hasAttribute('data-duration')
      ? parseFloat(elem.getAttribute('data-duration') || '0')
      : 0.6;
    const delay = elem.hasAttribute('data-delay')
      ? parseFloat(elem.getAttribute('data-delay') || '0')
      : 0;
    const offset = elem.hasAttribute('data-offset')
      ? parseFloat(elem.getAttribute('data-offset') || '0')
      : 60;
    const instant = elem.hasAttribute('data-instant') && elem.getAttribute('data-instant') !== 'false';
    const start = elem.getAttribute('data-start') || 'top 90%';
    const end = elem.getAttribute('data-end') || 'top 50%';
    const direction = elem.getAttribute('data-direction') || 'down';
    const useSpring = elem.hasAttribute('data-spring');
    const rotation = elem.hasAttribute('data-rotation')
      ? parseFloat(elem.getAttribute('data-rotation') || '0')
      : 0;
    const animationType = elem.getAttribute('data-animation-type') || 'from';

    // Reset base styling
    elem.style.opacity = '1';
    elem.style.filter = 'blur(0)';

    const spring = useSpring && springFactory ? springFactory(0.2, 0.8) : undefined;

    const animationProps: gsap.TweenVars = {
      duration,
      delay,
      ease: spring || 'power2.out',
    };

    if (rotation !== 0) {
      animationProps.rotation = rotation;
    }

    switch (direction) {
      case 'left':
        animationProps.x = -offset;
        break;
      case 'right':
        animationProps.x = offset;
        break;
      case 'down':
        animationProps.y = offset;
        break;
      case 'up':
      default:
        animationProps.y = -offset;
        break;
    }

    if (!instant) {
      animationProps.scrollTrigger = {
        trigger: elem,
        start,
        end,
        scrub: false,
      };
    }

    if (animationType === 'to') {
      animationProps.opacity = 1;
      animationProps.filter = 'blur(0)';
      gsap.to(elem, animationProps);
    } else {
      animationProps.opacity = 0;
      animationProps.filter = 'blur(16px)';
      gsap.from(elem, animationProps);
    }
  });
};

export const initHomePage33Animations = async () => {
  await ensureSpringer();
  await ensureStackCards();

  initRevealElements();
  requestStackCardsUpdate();
  if (typeof window !== 'undefined') {
    window.requestAnimationFrame(() => {
      requestStackCardsUpdate();
      window.dispatchEvent(new Event('scroll'));
    });
  }
};
