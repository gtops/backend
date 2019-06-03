import { AgeCategory } from "./age-category/AgeCategory";
import { Command } from "./Command/Command";
import { Competition } from "./competition/Competition";
import { Gender } from "./gender/Gender";
import { GroupInStandardParent } from "./groupIn-standard-parent/GroupInStandardParent";
import { Participant } from "./participant/Participant";
import { ParticipantOnCompetition } from "./participant-on-competition/ParticipantOnCompetition";
import { ResultGuide } from "./result-guide/ResultGuide";
import { ResultParticipantOnTrial } from "./result-participant-on-trial/ResultParticipantOnTrial";
import { Role } from "./role/Role";
import { StandardParent } from "./standard-parent/StandardParent";
import { Trial } from "./trial/Trial";
import { TrialOnCompetition } from "./trial-on-competition/TrialOnCompetition";
import { TrialOnGroup } from "./trial-on-group/TrialOnGroup";
import { Unit } from "./unit/Unit";
import { User } from "./user/User";

export const models = [
	User,
	AgeCategory,
	Competition,
	Command,
	Gender,
	Unit,
	TrialOnCompetition,
	TrialOnGroup,
	Trial,
	StandardParent,
	Role,
	GroupInStandardParent,
	Participant,
	ParticipantOnCompetition,
	ResultGuide,
	ResultParticipantOnTrial
];
