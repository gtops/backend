import { should } from "chai";
import { describe, Done, it } from "mocha";
import { userLoginData } from "../../data/AuthorizationData";
import { SERVER_AGENT } from "../../index";
import { EAuthorization } from "../../routes/EAuthorization";
import { token } from "../../scheme/authorization/token";

should();

// TODO: добавить заполнение бд

describe("Login tests", () => {
	it("Should get token", (done: Done) => {
		SERVER_AGENT.post(EAuthorization.LOGIN)
			.expect(200)
			.send(userLoginData)
			.end((error: Error, response: any) => {
				response.body.should.to.be.jsonSchema(token);
				done();
			});
	});
});

// TODO: отчищать всю бд после тестов
