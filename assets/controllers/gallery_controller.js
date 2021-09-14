import { Controller } from 'stimulus';
import 'bootstrap/dist/js/bootstrap.bundle.min';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="map" attribute will cause
 * this controller to be executed. The name "map" comes from the filename:
 * map_controller.js -> "map"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static targets = [
        'toggle',
        'modal',
        'imageIndex',
        'image',
        'next',
        'previous'
    ]

    connect() {
        const self = this;

        const handleChange = (e) => {
            e.preventDefault();

            let target = e.target;
            const path = e.path;
            let i = path.indexOf(target) || 0;

            while (!target.classList.contains('controls') && i < path.length) {
                i ++;
                target = path[i];
            }

            if (!target.hasAttribute('data-goto')) {
                return;
            }
            self.imageTarget.src = '/images/skateboarding.gif';
            const goto = parseInt(target.getAttribute('data-goto'));

            self.imageTarget.src = self.toggleTargets[goto].getAttribute('data-src');

            self.imageIndexTarget.innerText = goto + 1;
            setGotos(goto);
        }

        this.nextTarget.addEventListener('click', handleChange);

        this.previousTarget.addEventListener('click', handleChange);

        this.modalTarget.addEventListener('show.bs.modal', function(e) {
            self.imageTarget.src = e.relatedTarget.getAttribute('data-src');

            const index = parseInt(e.relatedTarget.getAttribute('data-index'));
            self.imageIndexTarget.innerText = index + 1;
            setGotos(index);
        });

        this.modalTarget.addEventListener('hidden.bs.modal', function(e) {
            self.imageTarget.src = '/images/skateboarding.gif';
        })

        function setGotos(index) {
            if (index > 0) {
                self.previousTarget.setAttribute('data-goto', index - 1);
                self.previousTarget.classList.remove('disabled');
            } else {
                self.previousTarget.removeAttribute('data-goto');
                self.previousTarget.classList.add('disabled');
            }

            if (index < self.toggleTargets.length - 1) {
                self.nextTarget.setAttribute('data-goto', index + 1);
                self.nextTarget.classList.remove('disabled');
            } else {
                self.nextTarget.removeAttribute('data-goto');
                self.nextTarget.classList.add('disabled');
            }
        }
    }
}
