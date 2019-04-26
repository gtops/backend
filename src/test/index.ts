import { before, describe, Done } from "mocha";
import { agent, SuperTest, Test } from "supertest";
import { Config } from "../config";
import { seed } from "../index";

export const SERVER_AGENT: SuperTest<Test> = agent(`${Config.test.server.url}`);

before((done: Done) => describe("Running server", () => seed.then(done).catch(done)));
