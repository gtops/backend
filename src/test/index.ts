import { before, describe, Done } from "mocha";
import { agent, SuperTest, Test } from "supertest";
import { config } from "../config/Config";
import { seed } from "../index";

export const SERVER_AGENT: SuperTest<Test> = agent(`${config.server.url}`);

before((done: Done) => describe("Running server", () => seed.then(done).catch(done)));
