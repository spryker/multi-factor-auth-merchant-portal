import { Component, NO_ERRORS_SCHEMA } from '@angular/core';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { AjaxActionService } from '@spryker/ajax-action';
import { MfaHandlerComponent } from './mfa-handler.component';

const LOGIN_URL = '/security-merchant-portal-gui/login_check';
const REDIRECT_RESPONSE = { actions: [{ type: 'redirect', url: '/dashboard-merchant-portal-gui/dashboard' }] };
const OPEN_MODAL_RESPONSE = {
    actions: [{ type: 'open-modal', url: '/multi-factor-auth-merchant-portal/user/get-enabled-types' }],
};

@Component({
    standalone: false,
    selector: 'mp-test-login-mfa-handler',
    template: `
        <form name="login-form" action="${LOGIN_URL}" method="post">
            <input name="login-form[username]" value="merchant@spryker.com" />
        </form>
        <mp-mfa-handler formSelector="form[name=login-form]" url="${LOGIN_URL}" [isLogin]="isLogin"></mp-mfa-handler>
    `,
})
class TestLoginComponent {
    isLogin = true;
}

@Component({
    standalone: false,
    selector: 'mp-test-mfa-handler',
    template: `
        <form name="login-form" action="${LOGIN_URL}" method="post">
            <input name="login-form[username]" value="merchant@spryker.com" />
        </form>
        <mp-mfa-handler formSelector="form[name=login-form]" url="${LOGIN_URL}"></mp-mfa-handler>
    `,
})
class TestDefaultComponent {}

describe('MfaHandlerComponent', () => {
    let httpTestingController: HttpTestingController;

    beforeEach(() => {
        TestBed.configureTestingModule({
            declarations: [MfaHandlerComponent, TestLoginComponent, TestDefaultComponent],
            providers: [
                provideHttpClient(),
                provideHttpClientTesting(),
                { provide: AjaxActionService, useValue: { handle: jest.fn() } },
            ],
            schemas: [NO_ERRORS_SCHEMA],
        });

        httpTestingController = TestBed.inject(HttpTestingController);
    });

    afterEach(() => {
        httpTestingController.verify();
    });

    function createFixture<T>(component: new () => T): ComponentFixture<T> {
        const fixture = TestBed.createComponent(component);
        document.body.appendChild(fixture.nativeElement);
        fixture.detectChanges();

        return fixture;
    }

    function submitForm(fixture: ComponentFixture<unknown>): boolean {
        const form: HTMLFormElement = fixture.nativeElement.querySelector('form[name=login-form]');

        return form.dispatchEvent(new Event('submit', { cancelable: true }));
    }

    describe('login form', () => {
        it('should send a second submission while the first one is pending as no request', () => {
            const fixture = createFixture(TestLoginComponent);

            submitForm(fixture);
            const isSecondSubmissionLeftToTheBrowser = submitForm(fixture);

            expect(httpTestingController.match(LOGIN_URL).length).toBe(1);
            expect(isSecondSubmissionLeftToTheBrowser).toBe(false);
        });

        it('should send no request for a submission after a response that redirects', () => {
            const fixture = createFixture(TestLoginComponent);
            submitForm(fixture);
            httpTestingController.expectOne(LOGIN_URL).flush(REDIRECT_RESPONSE);

            submitForm(fixture);

            httpTestingController.expectNone(LOGIN_URL);
        });

        it('should send a submission after a response that does not redirect', () => {
            const fixture = createFixture(TestLoginComponent);
            submitForm(fixture);
            httpTestingController.expectOne(LOGIN_URL).flush(OPEN_MODAL_RESPONSE);

            submitForm(fixture);

            httpTestingController.expectOne(LOGIN_URL);
        });

        it('should send a submission after the page is restored from the back-forward cache', () => {
            const fixture = createFixture(TestLoginComponent);
            submitForm(fixture);
            httpTestingController.expectOne(LOGIN_URL).flush(REDIRECT_RESPONSE);
            const pageShowEvent = new Event('pageshow');
            Object.defineProperty(pageShowEvent, 'persisted', { value: true });

            window.dispatchEvent(pageShowEvent);
            submitForm(fixture);

            httpTestingController.expectOne(LOGIN_URL);
        });
    });

    describe('form that is not a login form', () => {
        it('should send a request for every submission', () => {
            const fixture = createFixture(TestDefaultComponent);

            submitForm(fixture);
            submitForm(fixture);

            expect(httpTestingController.match(LOGIN_URL).length).toBe(2);
        });
    });
});
